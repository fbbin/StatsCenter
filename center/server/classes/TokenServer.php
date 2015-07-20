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
        $this->serv->send($fd, json_encode($json));
    }

    function onReceive($serv, $fd, $thread_id, $data)
    {
        $token = rtrim($data);
        $time = time();
        if (preg_match('#^u(\d+)_(\d+)_(.+)$#i', $token, $ar))
        {
            list (, $uid, $tkid, $ac_token) = $ar;
            $redis_key = '1100_202_' . $tkid;
            $token_info = \Swoole::$php->redis->get($redis_key);
            if (!$token_info)
            {
                $res = \Swoole::$php->db->query("select * from cw_utoken_0 where tkid={$tkid} limit 1");
                if (!$res)
                {
                    goto fail;
                }
                $token_info = $res->fetch();
                \Swoole::$php->redis->setex($redis_key, json_encode($token_info), self::EXPIRE);
            }

            if (!isset($token['uid']) or $uid != $token['uid'])
            {
                goto fail;
            }

            if (isset ($token_info ['ac_token']) and $token_info ['ac_token'] == $ac_token)
            {
                if ($token_info ['ctime'] < $time - self::EXPIRE)
                {
                    goto fail;
                }
                else
                {
                    $this->sendJson($fd, ['code' => 1]);
                }
            }
        }
        fail:
        $this->sendJson($fd, ['code' => self::RJ_ERR_TOKEN]);
    }
}