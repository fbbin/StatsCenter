<?php
namespace App;

require '/data/system/libraries/Service.php';
/**
 * Class Msg
 * @package App
 * 弹窗
 */
class Msg
{
    public $handler;
    public $worker_id;

    private $config;

    function __construct($handler)
    {
        $this->handler = $handler;
        $this->config = \Swoole::$php->config['msg']['master'];
    }

    function alert($msg)
    {
        $this->worker_id = $this->handler->alert->worker_id;
        $mobiles = explode(',',$msg['alert_mobiles']);
        if (!empty($mobiles))
        {
            \Swoole\Filter::safe($msg);
            $msg = $this->handler->build_msg($msg);
            $this->_send($mobiles,$msg);
        }
        else
        {
            $this->log("task worker {$this->worker_id} error.".print_r($msg,1));
        }
    }

    private function _send($mobiles,$message)
    {
        if (!empty($mobiles))
        {
            foreach ($mobiles as $number)
            {
                $service = new \Service('chelun');
                $res = $service->call('Common\SMS::send', $number, $message)->getResult();
                if ($res)
                {
                    $this->log("task worker {$this->worker_id} send msg success: $number  {$message}");
                }
                else
                {
                    $this->log("task worker {$this->worker_id} send msg failed: $number  {$message}");
                }
            }
        }
        else
        {
            $this->log("task worker {$this->worker_id} mobiles empty ");
        }
    }

    public function log($msg)
    {
        $this->handler->alert->log($msg);
    }
}
