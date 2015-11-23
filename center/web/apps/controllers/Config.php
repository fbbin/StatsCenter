<?php
namespace App\Controller;

use mobilemsg\service\Filter;
use Swoole;

require_once '/data/www/public/sdk/StatsCenter.php';
require_once '/data/www/public/sdk/CloudConfig.php';

class Config extends \App\LoginController
{
    function index()
    {
        $categorys = table('config_category', 'platform')->gets(array('project_id' => $this->projectId, 'order' => 'id asc'));
        if (empty($_GET['category']))
        {
            if (count($categorys) > 0)
            {
                $_GET['category'] = $categorys[0]['id'];
            }
            else
            {
                $_GET['category'] = 0;
            }
        }

        if ($_GET['category'])
        {
            $list = table('config_entity', 'platform')->gets(['category_id' => $_GET['category']]);
        }
        else
        {
            $list = [];
        }

        $users = table('user', 'platform')->getMap(['select' => 'id,realname'], 'realname');
        $this->assign('users', $users);
        $this->assign('list', $list);
        $this->assign('url_base', '/config/entity/?category=' . $_GET['category']);
        $this->assign('categorys', $categorys);
        $this->display();
    }

    function entity()
    {
        if (empty($_GET['category']))
        {
            return "错误的请求";
        }

        $table = table('config_entity', 'platform');
        if (!empty($_GET['id']))
        {
            $id = intval($_GET['id']);
            $entity = $table->get($id)->get();

            if (!empty($_GET['op']))
            {
                switch($_GET['op'])
                {
                    case 'delete':
                        $table->del($id);
                        return Swoole\JS::js_back('删除成功');
                    case 'push':
                        $this->pushConfig($entity);
                        return  Swoole\JS::js_back('下发成功');
                }
            }
        }
        else
        {
            $id = 0;
            $entity['content'] = '{}';
            $entity['name'] = '';
            $entity['ckey'] = '';
        }

        if (!empty($_POST['json']))
        {
            $name = trim($_POST['name']);
            $ckey = trim($_POST['ckey']);
            $entity['name'] = $name;
            $entity['ckey'] = $ckey;

            Swoole\Filter::safe($_POST['json']);
            $json = json_decode($_POST['json'], true);
            if (empty($json))
            {
                $msg['code'] = 501;
                $msg['message'] = '操作失败，JSON解析失败';
                goto msg;
            }

            $insert = [
                'name' => $name,
                'content' => $_POST['json'],
                'ckey' => $ckey,
                'category_id' => intval($_GET['category']),
                'project_id' => $this->projectId,
                'create_time' => time(),
                'owner_uid' => $this->uid,
            ];
            $entity['content'] = $_POST['json'];

            if (empty($id))
            {
                if ($table->exists(['orwhere' => ["name='{$name}'", "ckey='{$ckey}'"]]))
                {
                    $msg['code'] = 503;
                    $msg['message'] = '操作失败，此分类已存在';
                    goto msg;
                }
                $result = $table->put($insert);
                if ($result)
                {
                    $msg['code'] = 0;
                    $msg['insert_id'] = $id;
                    $msg['message'] = '添加成功';
                }
                else
                {
                    $msg['code'] = 504;
                    $msg['message'] = '操作失败';
                }
            }
            else
            {
                $result = $table->set($id, $insert);
                if ($result)
                {
                    $msg['code'] = 0;
                    $msg['insert_id'] = $id;
                    $msg['message'] = '修改成功';
                }
                else
                {
                    $msg['code'] = 505;
                    $msg['message'] = '修改失败';
                }
            }
            msg:
            $this->assign('msg', $msg);
        }
        $this->assign('entity', $entity);
        $this->assign('json', $entity['content']);
        $this->assign('name', $entity['name']);
        $this->assign('ckey', $entity['ckey']);
        $this->display();
    }

    function pushConfig($config)
    {
        $table = table('config_ip', 'platform');
        $ipList = $table->gets(['project_id' => $this->projectId]);
        debug($ipList);
    }

    function node()
    {
        $table = table('config_ip', 'platform');
        if (!empty($_GET['del']))
        {
            if ($table->del(intval($_GET['del'])))
            {
                $msg['code'] = 0;
                $msg['message'] = '删除成功';
            }
            else
            {
                $msg['code'] = 501;
                $msg['message'] = '删除失败';
            }
            goto msg;
        }

        if (!empty($_POST['ip']))
        {
            $ip = trim($_POST['ip']);
            if (!Swoole\Validate::ip($ip))
            {
                $msg['code'] = 501;
                $msg['message'] = 'IP格式错误';
                goto msg;
            }
            if ($table->exists(['ip' => $ip, 'project_id' => $this->projectId,]))
            {
                $msg['code'] = 502;
                $msg['message'] = '此机器已存在';
                goto msg;
            }
            $result = $table->put(['ip' => $ip,
                'project_id' => $this->projectId,
                'add_uid' => $this->uid,
            ]);
            if ($result)
            {
                $msg['code'] = 0;
                $msg['insert_id'] = $result;
                $msg['message'] = '添加成功';
            }
            else
            {
                $msg['code'] = 504;
                $msg['message'] = '操作失败';
            }
            msg:
            $this->assign('msg', $msg);
        }
        $users = table('user', 'platform')->getMap(['select' => 'id,realname'], 'realname');
        $this->assign('users', $users);
        $list = $table->gets(['project_id' => $this->projectId, 'order' => 'add_time']);
        $this->assign('list', $list);
        $this->display();
    }

    function category()
    {
        if (!empty($_POST['name']))
        {
            $name = trim($_POST['name']);
            $table = table('config_category', 'platform');
            if ($table->exists(['name' => $name]))
            {
                $msg['code'] = 501;
                $msg['message'] = '操作失败，此分类已存在';
            }
            else
            {
                $insert = ['name' => $name,
                    'intro' => trim($_POST['intro']),
                    'project_id' => $this->projectId,
                    'create_time' => time(),
                    'owner_uid' => $this->uid,
                ];
                $id = $table->put($insert);
                if ($id)
                {
                    $msg['code'] = 0;
                    $msg['insert_id'] = $id;
                    $msg['message'] = '添加成功';
                }
                else
                {
                    $msg['code'] = 502;
                    $msg['message'] = '操作失败';
                }
            }
            $this->assign('msg', $msg);
        }
        $this->display();
    }
}