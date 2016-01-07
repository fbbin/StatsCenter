<?php
namespace App\Controller;
use Swoole;

class Service
{
    const KEY_PREFIX = 'aopnet:';
    public $services = array(
        'service' => 'service:config:',//默认 服务化配置
        'queue' => 'queue:config:'//队列服务 配置
    );

    /**
     * 注册服务
     * method POST
     * @var array
     *  service 服务名称
     *  config_id 配置id
     *  env 环境
     */
    function reg()
    {
        if (empty($_POST)) {
            $return['msg'] = "params empty";
            goto failed;
        }
        if (empty($_POST['config_id']) or empty($_POST['env'])) {
            $return['msg'] = "config_id,env can not be empty";
            goto failed;
        }
        if (empty($_POST['data'])) {
            $return['msg'] = "data empty";
            goto failed;
        }
        $service = trim($_POST['service']);
        if (!array_key_exists($service,$this->services))
        {
            $return['msg'] = "service error";
            goto failed;
        }

        $config_id = trim($_POST['config_id']);
        $env = trim($_POST['env']);
        $data = (string)$_POST['data'];
        $config_id = $this->services[$service].$config_id;
        $key = self::KEY_PREFIX . $env . ':' . $config_id;
        $res = \Swoole::$php->redis("cluster")->set($key,$data);
        if (!$res)
        {
            $return['msg'] = "reg failed";
            goto failed;
        }
        else
        {
            $return['msg'] = "reg success";
            $return['code'] = 0;
            return json_encode($return);
        }
        failed:
        $return['code'] = 1;
        return json_encode($return);
    }

    function get()
    {
        if (empty($_GET)) {
            $return['msg'] = "params empty";
            goto failed;
        }

        if (empty($_GET['config_id']) or empty($_GET['env'])) {
            $return['msg'] = "config_id,env can not be empty";
            goto failed;
        }

        $service = trim($_GET['service']);
        if (!array_key_exists($service,$this->services))
        {
            $return['msg'] = "service error";
            goto failed;
        }

        $config_id = trim($_GET['config_id']);
        $env = trim($_GET['env']);
        $config_id = $this->services[$service].$config_id;
        $key = self::KEY_PREFIX . $env . ':' . $config_id;
        $res = \Swoole::$php->redis("cluster")->get($key);
        if ($res)
        {
            $return['msg'] = "get success";
            $return['code'] = 0;
            $return['data'] = $res;
            return json_encode($return);
        }
        $return['msg'] = "get failed";
        failed:
        $return['code'] = 1;
        return json_encode($return);
    }

    function gets()
    {
        if (empty($_GET)) {
            $return['msg'] = "params empty";
            goto failed;
        }

        if (empty($_GET['config_id']) or empty($_GET['env'])) {
            $return['msg'] = "config_id,env can not be empty";
            goto failed;
        }
        $config_ids = explode(",",trim($_GET['config_id']));

        $service = trim($_GET['service']);
        if (!array_key_exists($service,$this->services))
        {
            $return['msg'] = "service error";
            goto failed;
        }
        $env = trim($_GET['env']);
        $data = array();
        foreach ($config_ids as $config_id)
        {
            $key = self::KEY_PREFIX . $env . ':' . $this->services[$service].$config_id;
            $res = \Swoole::$php->redis("cluster")->get($key);

            if ($res)
            {
                $data[$config_id] = $res;
            }
        }
        if ($data)
        {
            $return['msg'] = "get success";
            $return['code'] = 0;
            $return['data'] = $data;
            return json_encode($return);
        }
        $return['msg'] = "get failed";
        failed:
        $return['code'] = 1;
        return json_encode($return);
    }
}