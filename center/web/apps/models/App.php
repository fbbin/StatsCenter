<?php
namespace App\Model;

class App extends \Swoole\Model
{
    public $table = 'app';

    function getOSList()
    {
        $os_list = \Swoole::$php->config['setting']['app_os'];
        unset($os_list[3]);
        return $os_list;
    }
}
