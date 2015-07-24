<?php
namespace StatsCenter;
use Swoole;

class AppStatsServer extends Server
{
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
                $table = table('stats_app');
                if ($stats)
                {
                    $list = json_decode($stats, true);
                    foreach($list as $li)
                    {
                        $put['client_network_type'] = $li['client']['network_type'];
                        $put['client_network_name'] = $li['client']['network_subtype'];
                        $put['http_url'] = $li['http']['url'];
                        $put['http_method'] = $li['http']['method'];
                        $put['http_body_length'] = $li['http']['body_length'];
                        $put['http_post_length'] = $li['http']['post_length'];
                        $put['http_data_code'] = $li['http']['data_code'];
                        $put['http_header_time'] = $li['http']['header_time'];
                        $put['http_total_titme'] = $li['http']['time'];
                        $put['http_json_parse'] = $li['http']['json_parse'];
                        $put['http_request_time'] = $li['http']['request_time'];
                        $table->put($put);
                    }
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