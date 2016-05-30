<?php
namespace App\Controller;

use Swoole;
use Swoole\Pager;

class App_host extends \App\LoginController
{
    private $project_id_regex = '/[a-zA-Z\d_]+/';
    private $env_id_regex = '/[a-zA-Z\d_]+/';
    private $url_regex = '/^https?:\/\//';

    function project_list()
    {
        $params = [
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'id desc',
        ];
        $data = table('app_project', 'platform')->gets($params, $pager);

        $this->assign('page_title', 'APP项目列表');
        $this->assign('data', $data);
        $this->assign('pager', $pager);
        $this->display();
    }

    function host_list()
    {
        $params = [
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'id desc',
            'where' => "ckey != ''",
        ];
        $data = table('web_project', 'platform')->gets($params, $pager);

        $redis = \Swoole::$php->redis('platform');
        $env_list = \Swoole::$php->config['setting']['env_list'];
        foreach ($data as &$row)
        {
            $row['ckey'] = trim($row['ckey']);
            $row['host_list'] = [];

            foreach ($env_list as $env_id => $env_name)
            {
                $host = $redis->hGet(\App\RedisKey::APP_HOST_LIST, strtolower("{$env_id}@{$row['ckey']}"));
                $row['host_list'][] = [
                    'env_id' => $env_id,
                    'env_name' => $env_name,
                    'host' => $host,
                ];
            }
        }
        unset($row);

        $this->assign('page_title', 'WEB项目接口管理');
        $this->assign('data', $data);
        $this->assign('pager', $pager);
        $this->display();
    }

    function edit_hosts()
    {
        $id = !empty($_GET['id']) ? intval($_GET['id']) : null;
        if (!is_null($id))
        {
            $project = table('web_project', 'platform')->get($id);
        }
        if (empty($project))
        {
            return $this->error('WEB项目不存在！');
        }
        $project['ckey'] = trim($project['ckey']);
        if ($project['ckey'] === '')
        {
            return $this->error('WEB项目的项目代号为空，请联系管理员！');
        }

        $env_list = \Swoole::$php->config['setting']['env_list'];
        $redis = \Swoole::$php->redis('platform');

        if (!empty($_POST))
        {
            $form_data = $_POST;
            $form_data['env_list'] = $env_list;
            $data = $this->validate($form_data, [$this, 'editHostsCheck'], $errors);

            if (empty($errors))
            {
                foreach ($data['host_list'] as $env_id => $host)
                {
                    $redis->hSet(\App\RedisKey::APP_HOST_LIST, strtolower("{$env_id}@{$project['ckey']}"), $host);
                }

                \App\Session::flash('msg', '编辑接口成功！');
                return $this->redirect("/app_host/edit_hosts?id={$id}");
            }
        }
        else
        {
            $form_data['host_list'] = [];
            foreach ($env_list as $env_id => $env_name)
            {
                $host = $redis->hGet(\App\RedisKey::APP_HOST_LIST, strtolower("{$env_id}@{$project['ckey']}"));
                $form_data['host_list'][$env_id] = $host;
            }
        }

        $host_list = [];
        foreach ($env_list as $env_id => $env_name)
        {
            $host_list[] = [
                'env_id' => $env_id,
                'env_name' => $env_name,
            ];
        }

        $page_title = sprintf('编辑「%s (%s)」接口', $project['name'], $project['ckey']);
        $this->assign('page_title', $page_title);
        $this->assign('form_data', !empty($form_data) ? $form_data : []);
        $this->assign('msg', \App\Session::get('msg'));
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->assign('host_list', $host_list);
        $this->display();
    }

    function app_host_list()
    {
        $id = !empty($_GET['id']) ? intval($_GET['id']) : null;
        if (!is_null($id))
        {
            $project = table('app_project', 'platform')->get($id);
        }
        if (empty($project))
        {
            return $this->error('APP项目不存在！');
        }

        $data = [];
        $app_key = strtolower(trim($project['app_key']));

        $redis = \Swoole::$php->redis('platform');
        $project_key_list = unserialize($redis->hGet(\App\RedisKey::APP_HOST_APP_KEY_HOSTS_MAP, $app_key));
        if (!empty($project_key_list))
        {
            $env_list = \Swoole::$php->config['setting']['env_list'];

            $project_key_list = array_map([table('web_project', 'platform')->db, 'quote'], $project_key_list);
            $params = [
                'where' => sprintf("ckey IN ('%s')", implode("', '", $project_key_list)),
            ];
            $project_list = table('web_project', 'platform')->gets($params);
            if (count($project_list) === count($project_key_list))
            {
                $project_list = array_rebuild($project_list, 'ckey');

                foreach ($project_key_list as $project_key)
                {
                    $row = $project_list[$project_key];
                    $row['host_list'] = [];

                    foreach ($env_list as $env_id => $env_name)
                    {
                        $hash_field = strtolower("{$env_id}@{$project_key}");
                        $host = $redis->hGet(\App\RedisKey::APP_HOST_LIST, $hash_field);
                        $row['host_list'][] = [
                            'env_id' => $env_id,
                            'env_name' => $env_name,
                            'host' => $host,
                        ];
                    }

                    $data[] = $row;
                }
            }
            else
            {
                $errors[] = '项目查询失败，请联系管理员！';
            }
        }

        $page_title = sprintf('「%s (%s)」APP项目接口列表', $project['name'], $project['app_key']);
        $this->assign('data', $data);
        $this->assign('project_id', $id);
        $this->assign('page_title', $page_title);
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display();
    }

    function edit_app_host_list()
    {
        $id = !empty($_GET['id']) ? intval($_GET['id']) : null;
        if (!is_null($id))
        {
            $project = table('app_project', 'platform')->get($id);
        }
        if (empty($project))
        {
            return $this->error('APP项目不存在！');
        }

        $redis = \Swoole::$php->redis('platform');
        $app_key = strtolower(trim($project['app_key']));

        if (!empty($_POST))
        {
            $form_data = $_POST;
            $data = $this->validate($form_data, [$this, 'editAppHostListCheck'], $errors);
            if (empty($errors))
            {
                $result = $redis->hSet(\App\RedisKey::APP_HOST_APP_KEY_HOSTS_MAP, $app_key, serialize($data['project_list']));
                if ($result !== false)
                {
                    \App\Session::flash('msg', '编辑接口列表成功！');
                    return $this->redirect("/app_host/edit_app_host_list?id={$id}");
                }
                else
                {
                    $errors[] = '编辑失败，请联系管理员！';
                }
            }
        }
        else
        {
            $params = [
                'order' => 'id desc',
                'where' => "ckey != ''",
            ];
            $project_list = table('web_project', 'platform')->gets($params);
            $project_key_list = unserialize($redis->hGet(\App\RedisKey::APP_HOST_APP_KEY_HOSTS_MAP, $app_key));
            if (is_array($project_key_list))
            {
                $form_data['project_list'] = [];
                foreach ($project_key_list as $project_key)
                {
                    $form_data['project_list'][$project_key] = true;
                }
            }
        }

        $page_title = sprintf('「%s (%s)」APP项目接口列表编辑', $project['name'], $project['app_key']);
        $this->assign('page_title', $page_title);
        $this->assign('project', $project);
        $this->assign('data', $project_list);
        $this->assign('msg', \App\Session::get('msg'));
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->assign('form_data', !empty($form_data) ? $form_data : []);
        $this->display();
    }

    function editHostsCheck(array $input, &$errors)
    {
        $output['host_list'] = array_get($input, 'host_list');

        if (is_array($output['host_list']))
        {
            foreach ($output['host_list'] as $env_id => &$host)
            {
                $env_name = isset($input['env_list'][$env_id]) ? $input['env_list'][$env_id] : null;
                if (is_null($env_name))
                {
                    $errors[] = '环境标识符非法，请联系管理员！';
                }

                $host = trim($host);
                if ($host !== '' && is_valid_url($host))
                {
                    $host = rtrim($host, '/');
                }
                else
                {
                    $errors[] = sprintf('%s的接口不符合正确的URL格式！', $env_name);
                }
            }
            unset($host);
        }
        else
        {
            $output['host_list'] = [];
        }

        return $output;
    }

    function editAppHostListCheck(array $input, &$errors)
    {
        $project_list = array_get($input, 'project_list', []);
        if (is_array($project_list))
        {
            $output['project_list'] = array_keys($project_list);
        }
        else
        {
            $errors[] = '接口列表编辑失败，请联系管理员！';
        }
        return $output;
    }
}
