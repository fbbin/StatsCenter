<?php
namespace App\Controller;

use Swoole;
use Swoole\Pager;

class App_host extends \App\LoginController
{
    function add_host()
    {
        if (empty($_POST))
        {
            $this->display_edit_host_page();
        }
        else
        {
            $this->edit_host_check($project_id, $env_id, $host, $error);
            $host = rtrim($host, '/');
            if (!empty($error))
            {
                return $this->display_edit_host_page($project_id, $env_id, $host, $error);
            }

            $identifier = strtolower($project_id . '-' . $env_id);

            Swoole::$php->redis('cluster')->hSet('app-host:id-host-map', $identifier, $host);
            $max_score_list =  Swoole::$php->redis('cluster')->zRevRangeByScore(
                'app-host:host-list',
                '+inf',
                '-inf',
                array(
                    'limit' => array(0, 1),
                    'withscores' => true,
                )
            );
            $max_score = reset($max_score_list);
            Swoole::$php->redis('cluster')->zAdd('app-host:host-list', $max_score + 1, $identifier);

            return Swoole\JS::js_goto('添加成功！', '/app_host/host_list');
        }
    }

    function edit_host()
    {
        $id = trim($this->value($_GET, 'id'));
        $pieces = explode('-', $id);

        if ($id === '' || count($pieces) !== 2)
        {
            $this->http->status(404);
            return;
        }

        $host = Swoole::$php->redis('cluster')->hGet('app-host:id-host-map', $id);
        list($project_id, $env_id) = explode('-', $id);

        if (empty($_POST))
        {
            return $this->display_edit_host_page($project_id, $env_id, $host);
        }
        else
        {
            $this->edit_host_check($project_id, $env_id, $host, $error);
            if (!empty($error))
            {
                return $this->display_edit_host_page($project_id, $env_id, $host, $error);
            }

            Swoole::$php->redis('cluster')->hSet('app-host:id-host-map', $id, $host);

            return Swoole\JS::js_goto('编辑成功', '/app_host/host_list');
        }

    }

    private function edit_host_check(&$project_id, &$env_id, &$host, &$error)
    {
        $project_id = trim($this->value($_POST, 'project_id'));
        $env_id = trim($this->value($_POST, 'env_id'));
        $host = trim($this->value($_POST, 'host'));

        $error = '';
        if (!preg_match('/[a-zA-Z\d_]+/', $project_id))
        {
            $error = '项目标识符格式不正确！';
        }
        elseif (!preg_match('/[a-zA-Z\d_]+/', $env_id))
        {
            $error = '环境标识符是格式不正确！';
        }
        elseif (!preg_match('/^https?:\/\//', $host))
        {
            $error = '接口地址格式不正确！';
        }
    }

    private function display_edit_host_page($project_id = null, $env_id = null, $host = null, $error = null)
    {
        $form['project_id'] = Swoole\Form::input('project_id', $project_id);
        $form['env_id'] = Swoole\Form::input('env_id', $env_id);
        $form['host'] = Swoole\Form::input('host', $host);

        $this->assign('error', $error);
        $this->assign('form', $form);
        $this->display('app_host/edit_host.php');
    }

    function delete_host()
    {
        $id = trim($this->value($_GET, 'id'));
        if ($id !== '')
        {
            if (Swoole::$php->redis('cluster')->zRem('app-host:host-list', $id))
            {
                Swoole::$php->redis('cluster')->hDel('app-host:id-host-map', $id);
            }
        }
        return Swoole\JS::js_goto('删除成功！', '/app_host/host_list');
    }

    function host_list()
    {
        $page = $this->value($_GET, 'page', 1, true);

        $per_page = 10;
        $start = ($page - 1) * $per_page;
        $end = $start + $per_page - 1;

        $host_list = \Swoole::$php->redis('cluster')->zRevRange('app-host:host-list', $start, $end);
        if (!empty($host_list))
        {
            $id_host_map = \Swoole::$php->redis('cluster')->hMGet('app-host:id-host-map', $host_list);
            $id_host_map = $id_host_map ? $id_host_map : array();
        }
        else
        {
            $id_host_map = array();
        }

        // 分页
        $total = \Swoole::$php->redis('cluster')->zCard('app-host:host-list');
        $pager = new Pager(array(
            'total' => $total,
            'perpage' => $per_page,
            'nowindex' => $page,
        ));

        $this->assign('id_host_map', $id_host_map);
        $this->assign('pager', array('render' => $pager->render()));
        $this->display();
    }

    function add_rule()
    {
        $form['uid'] = \Swoole\Form::input('uid');
        $form['openUDID'] = \Swoole\Form::input('openUDID');
        $form['identifier'] = \Swoole\Form::input('identifier');

        $this->assign('form', $form);

        $this->display('app_host/edit_rule.php');
    }

    function rule_list()
    {
        $data = array();
        $data[] = array(
            'uid' => '1',
            'identifier' => 'chelun-dev',
            'host' => 'http://chelun.com',
        );

        $data[] = array(
            'openUDID' => 'DEID76768DE-DEKDD33',
            'identifier' => 'common-prod',
            'host' => 'http://common.auto98.com/',
        );

        $this->assign('data', $data);

        $this->display();
    }
}
