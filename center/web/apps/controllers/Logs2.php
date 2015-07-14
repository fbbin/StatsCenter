<?php
namespace App\Controller;
use Swoole;
use App;

class Logs2 extends App\LoginController
{
    public $log_level = array(
        "TRACE",
        "INFO",
        "NOTICE",
        "WARNING",
        "ERROR",
    );

    function index()
    {
        $gets['select'] = 'id, name';
        $modules = table('module')->gets($gets);
        if (!empty($_GET['module']))
        {
            $module_id = intval($_GET['module']);
        }
        else
        {
            $module_id = $modules[0]['id'];
        }
        $this->data($module_id);
        $this->assign('modules', $modules);
        $this->display();
    }

    function data($module_id)
    {
        //\Swoole\Error::dbd();
        $log_table = 'logs2_';
        $gets['module'] = $module_id;

        if (empty($_GET['date_key']))
        {
            $_GET['date_key'] = date('Ymd');
            $log_table = $log_table . date('Ymd');
        }
        else
        {
            $log_table = $log_table . $_GET['date_key'];
        }

        if (isset($_GET['hour_start']))
        {
            $gets['where'][] = 'hour >= ' . $_GET['hour_start'];
        }
        if (isset($_GET['hour_end']))
        {
            $gets['where'][] = 'hour <= ' . $_GET['hour_end'];
        }
//        $start_hour = !empty($_GET['hour_start']) ? ($_GET['hour_start']) : '00';
//        $end_hour = !empty($_GET['hour_end']) ? ($_GET['hour_end']) : 23;
//        $start_time = $_GET['date_key'] . ' ' . $start_hour . ':00:' . '00';
//        $end_time = $_GET['date_key'] . ' ' . $end_hour . ':00:' . '00';

//
//        $gets['where'][] = 'addtime < ' . strtotime($end_time);

        if (!empty($_GET['client_ip']))
        {
            $gets['client_ip'] = $_GET['client_ip'];
        }
        if (isset($_GET['level']))
        {
            $gets['level'] = $_GET['level'];
        }
        $gets['page'] = !empty($_GET['page']) ? $_GET['page'] : 1;
        $gets['pagesize'] = 50;
        $gets['order'] = 'id asc';
        $logs = table($log_table)->gets($gets, $pager);
        $this->assign('pager', array('total' => $pager->total, 'render' => $pager->render()));

        $clients = $this->redis->sMembers('logs2:client:'.$module_id);
        $types = $this->redis->sMembers('logs2:type:'.$module_id);
        $subtypes = $this->redis->sMembers('logs2:subtype:'.$module_id);
        $_clients = $_types = $_subtypes = array();

        foreach ($clients as $k => $v)
        {
            $_clients[$v] = $v;
        }
        foreach ($types as $k => $v)
        {
            $_types[$v] = $v;
        }
        foreach ($subtypes as $k => $v)
        {
            $_subtypes[$v] = $v;
        }

        $client = empty($_GET['client']) ? 0 : $_GET['client'];
        $type = empty($_GET['type']) ? 0 : $_GET['type'];
        $subtype = empty($_GET['subtype']) ? 0 : $_GET['subtype'];

        $form['clients'] = \Swoole\Form::select('clients', $_clients, $client, '', array('class' => 'select2'));
        $form['types'] = \Swoole\Form::select('clients', $_types, $type, '', array('class' => 'select2'));
        $form['subtypes'] = \Swoole\Form::select('clients', $_subtypes, $subtype, '', array('class' => 'select2'));
        $this->assign('form', $form);
        $this->assign('logs', $logs);
    }

    function runtime()
    {
        $interface_id = intval($_GET['interface_id']);
        $module_id = intval($_GET['module_id']);
        if (empty($interface_id) or empty($module_id))
        {
            $return['status'] = 400;
            $return['msg'] = '操作失败';
        }
        $config = $this->config['websocket'];
        $this->assign('config', $config);
        $this->assign('return', $return);
        $this->display();
    }

    function get_ip()
    {
        $interface_id = (int)$_GET['interface_id'];
        $module_id = (int)$_GET['module_id'];
        if (!empty($interface_id) and !empty($module_id))
        {
            $client_ids = $this->redis->sMembers($module_id."_".$interface_id);
            if (!empty($client_ids))
            {
                $return['status'] = 0;
                $return['client_ids'] = $client_ids;
            }
            else
            {
                $return['status'] = 400;
            }
        }
        else
        {
            $return['status'] = 400;
        }
        echo json_encode($return);
    }

    function get_interface()
    {
        $module_id = (int)$_GET['module_id'];
        if (!empty($module_id))
        {
            $interface_ids = table("log_interface")->gets(array('module_id'=>$module_id));
            if (!empty($interface_ids))
            {
                $return['status'] = 0;
                $return['interface_ids'] = $interface_ids;
            }
            else
            {
                $return['status'] = 400;
            }
        }
        else
        {
            $return['status'] = 400;
        }
        echo json_encode($return);
    }
}