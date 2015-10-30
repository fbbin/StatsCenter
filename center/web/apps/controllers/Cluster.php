<?php
namespace App\Controller;

use Swoole;
use App\RedisKey;

require_once '/data/www/public/sdk/StatsCenter.php';
require_once '/data/www/public/sdk/CloudConfig.php';

class Cluster extends \App\LoginController
{
    static $envs = [
        'product' => '生产环境',
        'pre' => '预发布环境',
        'test' => '测试环境',
        'dev' => '开发环境',
        'local' => '本机',
    ];

    static $projects = [
        'chelun' => ['name' => '车轮社区', 'namespace' => 'CheLun',],
        'kaojiazhao' => ['name' => '考驾照', 'namespace' => 'KJZ',],
        'bypass' => ['name' => '旁路系统', 'namespace' => 'ByPass',],
    ];

    protected function getProjects()
    {
        foreach (self::$projects as $k => $v)
        {
            $this->redis('cluster')->hSet(RedisKey::CLUSTER_SERVICE_PROJECTS, $k, serialize($v));
        }

        $_tmp = [];
        $res = $this->redis('cluster')->hGetAll(RedisKey::CLUSTER_SERVICE_PROJECTS);
        foreach($res as $k => $v)
        {
            $_tmp[$k] = unserialize($v);
        }

        return $_tmp;
    }

    protected function getProject()
    {
        $projects = $this->getProjects();
        $project = empty($_GET['p']) ? 'chelun' : trim($_GET['p']);
        if (!isset($projects[$project]))
        {
            $project = 'chelun';
        }
        $this->assign('c_projs', $projects);
        $this->assign('c_proj', $project);
        return $project;
    }

    function index()
    {
        $projects = $this->getProjects();
        $project = $this->getProject();
        $list = [];
        foreach(self::$envs as $k => $v)
        {
            $key = 'aopnet:'.$k.':service:config:'.$project;
            $res = $this->redis('cluster')->get($key);
            if ($res === false)
            {
                $list[$k] = array('namespace' => $projects[$project]['namespace'], 'servers' => []);
            }
            else
            {
                $list[$k] = json_decode($res, true);
            }
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

        $project = $this->getProject();
        $projects = $this->getProjects();
        $env = $_GET['env'];
        $key = 'aopnet:'.$env.':service:config:'.$project;

        $res = $this->redis('cluster')->get($key);
        if ($res)
        {
            $config = json_decode($res, true);
        }
        else
        {
            $config = array('namespace' => $projects[$project]['namespace'], 'servers' => []);
        }

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
            if ($id !== false)
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