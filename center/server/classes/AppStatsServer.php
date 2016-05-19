<?php
namespace StatsCenter;
use Swoole;

class AppStatsServer extends Server
{
    const PORT = 8501;
    const EOF = "\r\n";

    protected $data = [];

    function sum($json)
    {
        $this->log($json);
        $list = json_decode($json, true);

        $this->serv->task($list);
        return;

        foreach ($list as $li)
        {
            $r = rand(0, 99);
            if ($r < 90) continue;

            $http_url = $li['http']['url'];
            $r = self::parserUrl($http_url);

            if (empty($r['host']) or empty($r['path']))
            {
                $this->log("no host or no path\n, URL=$http_url");
            }

            if (!isset($this->data[$r['host']][$r['path']]))
            {
                $count = array(
                    'json_parse_fail' => 0,
                    'time' => 0.0,
                    'request_time' => 0.0,
                    'header_time' => 0.0,
                );
                $this->data[$r['host']][$r['path']] = $count;
            }
            $count = &$this->data[$r['host']][$r['path']];

            //Http请求方法
            $http_method = $li['http']['method'];
            if (isset($count[$http_method]))
            {
                $count[$http_method]++;
            }
            else
            {
                $count[$http_method] = 1;
            }

            //客户端网络类型
            if (!empty($li['client_info']['network_sub_type']))
            {
                $client_network_type = $li['client_info']['network_type'];
                if (isset($count[$client_network_type]))
                {
                    $count[$client_network_type]++;
                }
                else
                {
                    $count[$client_network_type] = 1;
                }
            }

            //客户端网络类型
            if (!empty($li['client_info']['network_sub_type']))
            {
                $client_network_name = $li['client_info']['network_sub_type'];
                if (isset($count[$client_network_name]))
                {
                    $count[$client_network_name]++;
                }
                else
                {
                    $count[$client_network_name] = 1;
                }
            }

            if ($li['http']['json_parse'] == 0)
            {
                $count['json_parse_fail']++;
            }

            //数据Code
            $data_code = $li['http']['data_code'];
            if (isset($count['data_code'][$data_code]))
            {
                $count['data_code'][$data_code]++;
            }
            else
            {
                $count['data_code'][$data_code] = 1;
            }

            //HttpCode
            $http_code = $li['http']['code'];
            if (isset($count['http_code'][$http_code]))
            {
                $count['http_code'][$http_code]++;
            }
            else
            {
                $count['http_code'][$http_code] = 1;
            }
            //请求时间
            $count['time'] += $li['http']['time'];
            $count['request_time'] = $li['http']['request_time'];
            $count['header_time'] = $li['http']['header_time'];
        }
    }

    protected static function parserUrl($url)
    {
        $r = parse_url($url);
        //去掉斜杠
        $r['path'] = trim($r['path'], '/');
        parse_str($r['query'], $r['get']);
        return $r;
    }

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
                    $this->sum($stats);
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

    function onWorkerStart(\swoole_server $serv, $id)
    {
        if ($serv->taskworker)
        {
            return;
        }
//        $serv->tick(60000, function () use ($serv) {
//            //投递任务
//            $serv->task($this->data);
//            //清空数据
//            $this->data = [];
//        });
    }

    function onTask($serv, $task_id, $from_id, $data)
    {
        $tableName = 'stats_' . date('Ymd');
        $table = table($tableName);
        foreach ($data as $name1 => $host)
        {
            foreach ($host as $name2 => $cgi)
            {
                $cgi['host'] = $name1;
                $cgi['api'] = $name2;
                if (!table($table)->put($cgi) and \Swoole::$php->db->errno() == 1146)
                {
                    $this->createTable($table);
                    table($table)->put($cgi);
                }
            }
        }
    }

    protected function createTable($table)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
            `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
          `client_network_type` varchar(40) NOT NULL,
          `client_network_name` varchar(40) NOT NULL,
          `http_url` text NOT NULL,
          `http_method` varchar(10) NOT NULL,
          `http_body_length` int(11) NOT NULL,
          `http_post_length` int(11) NOT NULL,
          `http_data_code` int(11) NOT NULL,
          `http_header_time` float NOT NULL,
          `http_total_titme` float NOT NULL,
          `http_json_parse` tinyint(1) NOT NULL,
          `http_request_time` float NOT NULL,
          `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $r = \Swoole::$php->db->query($sql);
        //创建表成功后再建索引，避免重复创建索引
        if ($r)
        {
//            \Swoole::$php->db->query($create_index_sql);
//            \Swoole::$php->db->query($create_index_sql2);
        }
    }

    function run($_setting = array())
    {
        $default_setting = array(
            'worker_num' => 4,
            'task_worker_num' => 1,
        );
        $this->pid_file = $_setting['pid_file'];
        $setting = array_merge($default_setting, $_setting);
        $serv = new \swoole_http_server('0.0.0.0', self::PORT, SWOOLE_PROCESS);
        $serv->set($setting);
        $serv->on('Request', array($this, 'onRequest'));
        $serv->on('workerStart', array($this, 'onWorkerStart'));
        $serv->on('finish', array($this, 'onWorkerStart'));
        $serv->on('task', array($this, 'onTask'));
        $serv->on('finish', array($this, 'onFinish'));
        $this->serv = $serv;
        $this->serv->start();
    }
}


