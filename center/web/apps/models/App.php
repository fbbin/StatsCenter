<?php
namespace App\Model;

class App extends \Swoole\Model
{
    public $table = 'app';

    public function getOSList()
    {
        $os_list = \Swoole::$php->config['setting']['app_os'];
        unset($os_list[3]);
        return $os_list;
    }

    public function getOSName($os)
    {
        $os = intval($os);
        if (!empty(\Swoole::$php->config['setting']['app_os'][$os]))
        {
            return \Swoole::$php->config['setting']['app_os'][$os];
        }
        else
        {
            return \Swoole::$php->config['setting']['app_os_name'][APP_OS_UNKNOWN];
        }
    }
}
