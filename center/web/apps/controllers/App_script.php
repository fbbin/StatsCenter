<?php
namespace App\Controller;

use Swoole;

class App_script extends \App\LoginController
{
    function index()
    {
        $table = table('app_script', 'platform');

        if (isset($_GET['entity']))
        {
            $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
            $this->entity($id);
        }
        else
        {
            if (isset($_GET['del']))
            {
                $id = empty($_GET['del']) ? 0 : intval($_GET['del']);
                $table->del($id);
            }

            $gets['order'] = 'update_time desc';
            $data = $table->gets($gets);

            $users = table('user', 'platform')->getMap(['select' => 'id,realname'], 'realname');

            $this->assign('users', $users);
            $this->assign('data', $data);
            $this->display();
        }
    }

    static $appList = [
        'chelun' => '车轮(chelun)',
        'kaojiazhao' => '考驾照(kaojiazhao)',
        'chaweizhang' => '查违章(chaweizhang)',
        'fulidaquan' => '福利大全(fulidaquan)',
    ];

    protected function entity($id = 0)
    {
        $table = table('app_script', 'platform');
        $object = null;
        if (!empty($id))
        {
            $object = $table->get($id);
            $entity = $object->get();
        }
        else
        {
            $entity['name'] = '';
            $entity['version'] = '';
            $entity['content'] = '';
        }

        if (!empty($_POST))
        {
            $put['content'] = $_POST['content'];
            $put['name'] = $_POST['name'];
            $put['version'] = $_POST['version'];
            $put['owner_uid'] = $this->userinfo['id'];
            $put['update_uid'] = $this->userinfo['id'];
            $put['update_time'] = time();

            if ($id)
            {
                $table->set($id, $put);
            }
            else
            {
                $table->put($put);
            }

            return Swoole\JS::js_goto("操作成功", '/app_script/index/');
        }

        $form['name'] = Swoole\Form::select('name', self::$appList, $entity['name'], false, array('class' => 'select2 select2-offscreen', 'style' => "width:100%"));

        $this->assign('entity', $entity);
        $this->assign('form', $form);
        $this->display('app_script/entity.php');
    }
}
