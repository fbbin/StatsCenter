<?php
namespace App\Controller;

use Swoole;

class Cluster extends \App\LoginController
{
    static $envs = [
        'product' => '生产环境',
        'pre' => '预发布环境',
        'test' => '测试环境',
        'dev' => '开发环境',
        'local' => '本机',
    ];

    function index()
    {
        $list = [];
        foreach(self::$envs as $k => $v)
        {
            $key = 'aopnet:'.$k.':service:config:chelun';
            $list[$k] = json_decode($this->redis('cluster')->get($key), true);
            $list[$k]['env.name'] = $v;
        }
        $this->assign('list', $list);
        $this->display();
    }

    function node()
    {
        if (empty($_GET['env']))
        {
            return "缺少参数";
        }

        $env = $_GET['env'];
        $key = 'aopnet:'.$env.':service:config:chelun';
        $config = json_decode($this->redis('cluster')->get($key), true);

        if (!empty($_POST['ip']) and !empty($_POST['port']))
        {
            $ip = trim($_POST['ip']);
            if (!Swoole\Validate::ip($ip))
            {
                return Swoole\JS::js_back("IP格式错误");
            }
            $port = intval(trim($_POST['port']));
            if ($port < 1 or $port > 65535)
            {
                return Swoole\JS::js_back("PORT格式错误");
            }
            $newServer = "$ip:$port";
            if (in_array($newServer, $config['servers']))
            {
                return Swoole\JS::js_back("Server已存在");
            }
            $config['servers'][] = $newServer;
            if ($this->redis('cluster')->set($key, json_encode($config)) === false)
            {
                return Swoole\JS::js_back("写入Redis失败");
            }
        }
        elseif (!empty($_GET['del']))
        {
            if (count($config['servers']) == 1)
            {
                return Swoole\JS::js_back("仅剩1个节点无法再删除");
            }
            $del = trim($_GET['del']);
            $id = array_search($del, $config['servers']);
            if ($id)
            {
                unset($config['servers'][$id]);
                if ($this->redis('cluster')->set($key, json_encode($config)) === false)
                {
                    return Swoole\JS::js_back("写入Redis失败");
                }
            }
        }
        $this->assign('config', $config);
        $this->display();
    }
}