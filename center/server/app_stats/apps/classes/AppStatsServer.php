<?php
namespace App;
use Swoole;

class AppStatsServer extends Swoole\Object
{
    protected $pid_file;

    /**
     * @var \swoole_server
     */
    protected $serv;
    const PORT = 8501;
    const EOF = "\r\n";

    function onRequest(\swoole_http_request $req, \swoole_http_response $resp)
    {
        $path = trim($req->server['request_uri'], '/');
        if ($path == 'app/stats')
        {
            if ($req->server['request_method'] != 'POST')
            {
                $resp->status(403);
                $resp->end("<h1>No POST Data</h1>");
            }
            else
            {
                $data = $req->rawContent();
                $stats = gzdecode($data);
                if ($stats)
                {
                    $this->log($stats);
                }
                else
                {
                    $this->log("gzdecode failed, fd={$req->fd}, length={$req->header['content-length']}");
                }
                $resp->end('{"code": 1}');
            }
        }
        else
        {
            $resp->status(404);
            $resp->end("<h1>Page Not Found</h1>");
        }
    }

    function setLogger($log)
    {
        $this->log = $log;
    }

    function log($msg)
    {
        $this->log->info($msg);
    }

    function run($_setting = array())
    {
        $default_setting = array(
            'worker_num' => 4,
        );
        $this->pid_file = $_setting['pid_file'];
        $setting = array_merge($default_setting, $_setting);
        $serv = new \swoole_http_server('0.0.0.0', self::PORT, SWOOLE_PROCESS);
        $serv->set($setting);
        $serv->on('Request', array($this, 'onRequest'));
        $this->serv = $serv;
        $this->serv->start();
    }
}