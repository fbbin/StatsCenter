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
                        $this->log("get from db failed. tkid={$tkid}");
                        goto fail;
                    }
                    $token_info = $res->fetch();
                    \Swoole::$php->redis->setex($redis_key, json_encode($token_info), self::EXPIRE);
                }
                else
                {
                    $token_info = json_decode($token_info, true);
                }

                if (!isset($token_info['uid']) or $uid != $token_info['uid'])
                {
                    $this->log("uid error, tk_uid={$token_info['uid']}, in_uid={$uid}");
                    goto fail;
                }

                if (isset ($token_info ['ac_token']) and $token_info ['ac_token'] == $ac_token)
                {
                    if ($token_info ['ctime'] < $time - self::EXPIRE)
                    {
                        $this->log("token expire, ctime={$token_info ['ctime']}");
                        goto fail;
                    }
                    else
                    {
                        $this->sendJson($fd, ['code' => 1]);
                        return;
                    }
                }
            }
            fail:
            $this->sendJson($fd, ['code' => self::RJ_ERR_TOKEN]);
        }
        else
        {
            $this->serv->send($fd, "ERR|02" . self::EOF);
            return;
        }
    }
}