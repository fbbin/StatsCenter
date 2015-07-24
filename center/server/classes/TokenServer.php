<?php
namespace StatsCenter;

class TokenServer extends Server
{
    const PORT = 9908;
    const EOF = "\r\n";
    /**
     * token过期时间(秒)
     */
    const EXPIRE = 2592000;
    const RJ_ERR_TOKEN = 6;

    function run($_setting = array())
    {
        $default_setting = array(
            'dispatch_mode' => 3,
            'max_request' => 0,
            'open_eof_split' => true,
            'package_eof' => self::EOF,
        );

        define('SWOOLE_SERVER', true);
        $setting = array_merge($default_setting, $_setting);
        $serv = new \swoole_server('0.0.0.0', self::PORT, SWOOLE_PROCESS);
        $serv->set($setting);
        $serv->on('receive', array($this, 'onReceive'));
        $this->serv = $serv;
        $this->serv->start();
    }

    function sendJson($fd, $json)
    {
        $this->serv->send($fd, json_encode($json) . self::EOF);
    }

    function onReceive($serv, $fd, $thread_id, $data)
    {
        $cmd = explode("|", trim($data), 3);
        if (count($cmd) < 2)
        {
            $this->serv->send($fd, "ERR|01" . self::EOF);
            return;
        }
        if ($cmd[0] == 'check')
        {
            $token = $cmd[1];
            $time = time();


        }
        else
        {
            $this->serv->send($fd, "ERR|02" . self::EOF);
            return;
        }
    }
}