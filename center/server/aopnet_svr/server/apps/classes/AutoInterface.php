<?php
namespace App;

class AutoInterface
{
    protected $pid_file;
    public $log;

    /**
     * @var \swoole_server
     */
    protected $serv;
    const SVR_PORT_AOP = 9904;
    const EOF = "\r\n";

    function onReceive(\swoole_server $serv, $fd, $from_id, $data)
    {
        $_key = explode(' ', trim($data));
        if (count($_key) != 3)
        {
            return;
        }
        if ($_key[0] == 'GET')
        {
            $key = $this->getKey($_key[1], $_key[2]);
            $this->serv->send($fd, $key);
        }
        else
        {
            $this->serv->send($fd, "unkown".self::EOF);
        }
    }

    private function getKey($module_id, $interface_key)
    {
        $interface_key = \Swoole\Filter::escape($interface_key);
        $table = 'interface';
        $params['select'] = "id";
        $params['name'] = $interface_key;
        $params['module_id'] = $module_id;
        $data = table($table)->gets($params);
        if (!empty($data))
        {
            return $data[0]['id'];
        }
        else
        {
            $put['name'] = $interface_key;
            $put['alias'] = $interface_key;
            $put['module_id'] = $module_id;
            return table($table)->put($put);
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
            'open_eof_check' => true,
            'open_eof_split' => true,
            'package_eof' => self::EOF,
        );
        $this->pid_file = $_setting['pid_file'];
        $setting = array_merge($default_setting, $_setting);
        $serv = new \swoole_server('0.0.0.0', self::SVR_PORT_AOP, SWOOLE_PROCESS, SWOOLE_TCP);
        $serv->set($setting);
        $serv->on('receive', array($this, 'onReceive'));
        $this->serv = $serv;
        $this->serv->start();
    }
}