<?php
namespace App\Controller;

class App_release extends \App\LoginController
{
    function release_list()
    {
        $query_params = [
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'id desc',
            // 'where' => '`enable` = ' . APP_STATUS_ENABLED,
        ];
        $data = table('app', 'platform')->gets($query_params, $pager);

        $os_list = model('App')->getOSList();
        foreach ($data as &$row)
        {
            if (isset($os_list[$row['os']]))
            {
                $row['os_name'] = $os_list[$row['os']];
            }
            else
            {
                $row['os_name'] = \Swoole::$php->config['setting']['app_os_name'][APP_OS_UNKNOWN];
                $row['os'] = APP_OS_UNKNOWN;
            }
        }
        unset($row);

        $this->assign('pager', $pager);
        $this->assign('data', $data);
        $this->display();
    }

    function edit_app_version()
    {
    }
}
