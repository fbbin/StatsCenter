<?php
namespace App;
use StatsCenter;

class LogServer2 extends StatsCenter\Server
{
    const PORT = 9905;
    const EOF  = "\r\n";

    function __construct()
    {
        if (get_cfg_var('env.name') == 'local' or get_cfg_var('env.name') == 'dev')
        {
            \Swoole::$php->config->setPath(WEBPATH . '/apps/configs/dev/');
        }
    }

    function run($_setting = array())
    {
        $default_setting = array(
            'dispatch_mode' => 3,
            'max_request' => 0,
            'open_eof_split' => true,
            'package_eof' => self::EOF,
        );

        $setting = array_merge($default_setting, $_setting);
        $serv = new \swoole_server('0.0.0.0', self::PORT, SWOOLE_PROCESS);
        $serv->set($setting);
        $serv->on('receive', array($this, 'onReceive'));
        $this->serv = $serv;
        $this->serv->start();
    }

    function onReceive($serv, $fd, $thread_id, $data)
    {
        $info = $this->serv->connection_info($fd);
        $parts = explode("\n", $data, 2);
        $put = array();
        list($put['module'], $put['level'], $put['type'], $put['subtype'], $put['uid']) = explode("|", $parts[0]);
        $put['content'] = rtrim($parts[1]);
        $put['hour'] = date('H');
        $put['ip'] = $info['remote_ip'];
        $table = 'logs2_'.date('Ymd');
        table($table)->put($put);

        \Swoole::$php->redis->sAdd('logs2:client:' . $put['module'], $put['ip']);
        \Swoole::$php->redis->sAdd('logs2:type:' . $put['module'], $put['type']);
        \Swoole::$php->redis->sAdd('logs2:subtype:' . $put['module'], $put['subtype']);
    }
}