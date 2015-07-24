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
        $this->assign('module_id', $module_id);
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
        if (!empty($_GET['type']))
        {
            $gets['type'] = $_GET['type'];
        }
        if (!empty($_GET['subtype']))
        {
            $gets['subtype'] = $_GET['subtype'];
        }
        if (!empty($_GET['client']))
        {
            $gets['ip'] = $_GET['client'];
        }
        if (!empty($_GET['level']))
        {
            $gets['level'] = $_GET['level'] - 1;
        }
        if (!empty($_GET['uid']))
        {
            if (is_numeric($_GET['uid']))
            {
                $gets['uid'] = $_GET['uid'];
            }
            else
            {
                $gets['ukey'] = $_GET['uid'];
            }
        }

        if (isset($_GET['hour_start']))
        {
            $gets['where'][] = 'hour >= ' . $_GET['hour_start'];
        }
        if (isset($_GET['hour_end']))
        {
            $gets['where'][] = 'hour <= ' . $_GET['hour_end'];
        }

        //排序
        if (!empty($_GET['order']))
        {
            $gets['order'] = str_replace('_', ' ', $_GET['order']);
        }
        else
        {
            $gets['order'] = 'id desc';
        }

        //页数
        if (!empty($_GET['pagesize']))
        {
            $gets['pagesize'] = intval($_GET['pagesize']);
        }
        else
        {
            $gets['pagesize'] = 50;
        }

        $gets['page'] = !empty($_GET['page']) ? $_GET['page'] : 1;
        $logs = table($log_table)->gets($gets, $pager);
        $this->assign('pager', array('total' => $pager->total, 'pagesize' => $gets['pagesize'], 'render' => $pager->render()));

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
        $type = empty($_GET['type']) ? '' : $_GET['type'];
        $subtype = empty($_GET['subtype']) ? '' : $_GET['subtype'];

        $form['clients'] = \Swoole\Form::select('client', $_clients, $client, '', array('class' => 'select2'));
        $form['types'] = \Swoole\Form::select('type', $_types, $type, '', array('class' => 'select2'));
        $form['subtypes'] = \Swoole\Form::select('subtype', $_subtypes, $subtype, '', array('class' => 'select2'));
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