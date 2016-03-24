<?php
namespace App\Controller;
use mobilemsg\service\Filter;
use Swoole;
use App;

class Stats extends \App\LoginController
{
    //$_SESSION['userinfo']['yyuid']
    static $width = array(
        '10%','10%','10%','10%','10%','10%','10%','30%'
    );

    function home()
    {
        $this->display();
    }

    function index()
    {
        $this->getInterfaceInfo();
        $table = table('stats_sum_'.str_replace('-', '', $_GET['date_key']));
        $gets = [
            'module_id' => $_GET['module_id'],
            'order' => 'interface_id',
            'pagesize' => 20,
            'page' => empty($_GET['page']) ? 1 : intval($_GET['page']),
        ];

        if (!empty($_GET['orderby']))
        {
            $gets['order'] = $_GET['orderby'];
            if (empty($_GET['desc']))
            {
                $gets['order'] .= ' asc';
            }
            else
            {
                $gets['order'] .= ' desc';
            }
        }

        if (isset($_GET['interface_name']))
        {
            $_GET['interface_name'] = trim($_GET['interface_name']);
            Swoole\Filter::safe($_GET['interface_name']);
            if (!empty($_GET['interface_name']))
            {
                $gets['like'] = ['interface_name', '%' . $this->db->quote($_GET['interface_name']) . '%'];
            }
        }

        if (!empty($_GET['interface_id']))
        {
            $gets['interface_id'] = intval($_GET['interface_id']);
        }
        /**
         * @var Swoole\Pager
         */
        $pager = null;
        $data = $table->gets($gets, $pager);
        $this->assign('total', $pager->total);
        $this->assign('pager', $pager->render());
        $this->assign('data', $data);
        $this->display();
    }

    function detail()
    {
        $this->assign('width', self::$width);
        $this->getInterfaceInfo();

        $moduleId = intval($_GET['module_id']);
        $moduleInfo = table('module')->get($moduleId)->get();
        if (empty($moduleInfo))
        {
            $this->http->status(404);
            return "不存在的模块";
        }

        $interfaceId = intval($_GET['interface_id']);
        $interfaceInfo = table('interface')->get($interfaceId)->get();

        if (empty($interfaceInfo))
        {
            $this->http->status(404);
            return "不存在的接口";
        }

        $table = table('stats_'.str_replace('-', '', $_GET['date_key']));
        $pager = null;

        $gets = [
            'module_id' => $_GET['module_id'],
            'interface_id' => intval($_GET['interface_id']),
            'order' => 'id asc',
        ];

        $this->filterHour($gets);

        $data = $table->gets($gets, $pager);
        foreach($data as &$d)
        {
            if ($d['total_count'] == 0)
            {
                $d['succ_rate'] = '100';
                $d['succ_count']  = 0;
            }
            else
            {
                $d['succ_count'] = $d['total_count'] - $d['fail_count'];
                $d['succ_rate'] = round(($d['succ_count'] / $d['total_count']) * 100, 2);
                $d['time_str'] = App\StatsData::getTimerStr($d['time_key']) . ' ~ ' . App\StatsData::getTimerStr($d['time_key'] + 1);
            }
            $d['interface_name'] = $interfaceInfo['name'];
        }
        $this->assign('data', $data);
        $this->display('stats/detail_interface.php');
    }

    /**
     * 最近一小时的统计数据，只能使用JS
     */
    function last_hour()
    {
        if (empty($_GET['hour_start']))
        {
            $_GET['hour_start'] = App\StatsData::fillZero4Time(date('H', time() - 3600));
        }
        $this->getInterfaceInfo();
        $this->display();
    }

    /**
     * 获取接口相关信息
     * @throws \Exception
     */
    protected function getInterfaceInfo()
    {
        //\Swoole\Error::dbd();
        $gets['select'] = 'id, name';
        $gets['project_id'] = $this->projectId;

        $modules = table('module')->gets($gets);
        if (empty($_GET['date_key']))
        {
            $_GET['date_key'] = date('Y-m-d');
        }

        if (empty($_GET['module_id']))
        {
            $_GET['module_id'] = $modules[0]['id'];
        }

        $interface_ids = $this->redis->sMembers($_GET['module_id']);
        if (!empty($interface_ids))
        {
            $_ip = array();
            $_ip['in'] = array('id',implode(',',$interface_ids));
            $interfaces = table('interface')->gets($_ip);
        }
        else
        {
            $gets['module_id'] = intval($_GET['module_id']);
            $interfaces = table('interface')->gets($gets);
        }

        if (empty($_GET['interface_id']))
        {
            $_GET['interface_id'] = 0;
        }
        $this->assign('interfaces', $interfaces);
        $this->assign('modules', $modules);
    }

    function getInterface()
    {
        $module_id = (int)$_GET['module_id'];
        $gets['select'] = 'id, name';
        $gets['module_id'] = $module_id;
        $modules = table('interface')->getMap($gets,'name');
        $return = array();
        if (!empty($modules))
        {
            $return['status'] = 200;
            $return['data'] = $modules;
        }
        else
        {
            $return['status'] = 400;
        }
        return json_encode($return,JSON_NUMERIC_CHECK);
    }

    function detail_data()
    {
        if (empty($_GET['interface_id']) or empty($_GET['module_id']) or empty($_GET['type']))
        {
            return "需要interface_id/module_id参数";
        }
        $param = $_GET;
        unset($param['type']);
        $param['select'] = 'ip, interface_id, time_key, total_count, fail_count, total_time, total_fail_time, max_time, min_time';

        $date_key = empty($param['date_key']) ? date('Y-m-d') : $param['date_key'];
        $table = $this->getTableName($date_key, $_GET['type'] == 'server' ? 1 : 2);
        $data = table($table)->gets($param);

        return json_encode($data);
    }

    protected function filterHour(&$param)
    {
        if (!empty($_GET['hour_start']))
        {
            $param['where'][] = 'time_key >= ' . (intval($_GET['hour_start']) * 12);
        }
        if (!empty($_GET['hour_end']))
        {
            $param['where'][] = 'time_key < ' . (intval($_GET['hour_end']) * 12);
        }
    }

    function client()
    {
        $_GET['type'] = 'client';
        $this->assign('force_reload', true);
        $this->getInterfaceInfo();
        $this->display('stats/detail_ip.php');
    }

    function server()
    {
        $_GET['type'] = 'server';
        $this->assign('force_reload', true);
        $this->getInterfaceInfo();
        $this->display('stats/detail_ip.php');
    }

    function getTableName($date_key, $type = 3)
    {
        $table_prefix = 'stats';
        switch($type)
        {
            case 1:
                $table_prefix .= '_server';
                break;
            case 2:
                $table_prefix .= '_client';
                break;
            default:
                break;
        }
        return $table_prefix . '_' . str_replace('-', '', $date_key);
    }

    function fail()
    {
        $gets['interface_id'] = $_GET['interface_id'];
        $gets['module_id'] = $_GET['module_id'];
        $table = $this->getTableName($_GET['date_key']);

        if (!empty($_GET['time_key']) or $_GET['time_key'] == '0')
        {
            $gets['time_key'] = $_GET['time_key'];
        }
        $gets['select'] = 'time_key, ret_code, fail_server';
        $data = table($table)->gets($gets);
        $ret_code = $fail_server = array();
        foreach($data as $d)
        {
            //$d['time_key']
            $ret_code[] = json_decode($d['ret_code'], true);
            $fail_server[]  = json_decode($d['fail_server'], true);
        }
        $this->assign('ret_code', $ret_code);
        $this->assign('fail_server', $fail_server);
        $this->display();
    }

    function succ()
    {
        $gets['interface_id'] = $_GET['interface_id'];
        $gets['module_id'] = $_GET['module_id'];
        $table = $this->getTableName($_GET['date_key']);
        if (!empty($_GET['time_key']) or $_GET['time_key'] == '0')
        {
            $gets['time_key'] = $_GET['time_key'];
        }
        $gets['select'] = 'time_key, succ_ret_code, succ_server';
        $data = table($table)->gets($gets);
        $ret_code = $fail_server = array();
        foreach($data as $d)
        {
            //$d['time_key']
            $ret_code[] = json_decode($d['succ_ret_code'], true);
            $succ_server[]  = json_decode($d['succ_server'], true);
        }
        $this->assign('succ_ret_code', $ret_code);
        $this->assign('succ_server', $succ_server);
        $this->display();
    }

    function history_data ()
    {
        if (empty($_GET['module_id']) or empty($_GET['interface_id']))
        {
            return $this->message(5001, "require module_id and interface_id");
        }
        $param = $_GET;

        $param['date_start'] = !empty($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d');
        $param['date_end'] = !empty($_GET['date_end']) ? $_GET['date_end'] : date('Y-m-d', time() - 86400);
        $param['date_key'] = $_GET['date_start'];

        $d1 = $this->data($param, false, false);

        $param['date_key'] = $_GET['date_end'];
        $d2 = $this->data($param, false, false);

        return json_encode(array('data1' => $d1, 'data2' => $d2));
    }

    function data($param = array(), $ret_json = true, $get_interface = true)
    {
        //Swoole\Error::dbd();
        if (count($param) < 1)
        {
            $param = $_GET;
        }
        $ifs = array();
        if ($get_interface)
        {
            if (!empty($_GET['module_id']))
            {
                $gets['module_id'] = intval($_GET['module_id']);
                $sql = "select id,name,alias from interface";
                $ifs = $this->db->query($sql)->fetchall();
            }
            else
            {
                $ifs = table('interface')->all()->fetchall();
            }
        }

        if (!empty($_GET['interface_id']))
        {
            $gets['interface_id'] = intval($_GET['interface_id']);
            $ret['interface'] = $ifs;
        }
        else
        {
            $ids = array();
            foreach ($ifs as $if)
            {
                $ids[] = $if['id'];
            }
            //$gets['in'] = array('interface_id', join(',', $ids));
            $ret['interface'] = $ifs;
        }

        $gets['select'] = 'interface_id, module_id, time_key, total_count, fail_count, total_time, total_fail_time, max_time, min_time';

        if (!empty($param['hour_start']))
        {
            $gets['where'][] = 'time_key >= ' . (intval($param['hour_start']) * 12);
            $hour_start = $param['hour_start'].':00';
            unset($param['hour_start']);
        }
        else
        {
            $hour_start = '00:00';
        }

        $date_key = empty($param['date_key']) ? date('Y-m-d') : $param['date_key'];
        $table = $this->getTableName($date_key);

        if (!empty($param['hour_end']))
        {
            $gets['where'][] = 'time_key < '.intval($param['hour_end'] + 1) * 12;
            $hour_end = $param['hour_end'].':59';
            unset($param['hour_end']);
        }
        else
        {
            $hour_end = '23:59';
        }

        $data = table($table)->gets($gets);
        if (!empty($data))
        {
            $ret['status'] = 200;
        }
        else
        {
            $ret['status'] = 400;
        }

        $ret['stats'] = $data;
        $ret['date'] = $date_key;
        $ret['time_str'] = $hour_start . ' ~ ' . $hour_end;

        $this->http->header('Content-Type', 'application/json');
        return $ret_json ? json_encode($ret, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE) : $ret;
    }

    function modules()
    {
        //$this->db->debug = true;
        if (is_numeric($_GET['q']))
        {
            $gets['id'] = $_GET['q'];
        }
        else
        {
            $gets['like'] = array('name', $_GET['q'].'%');
        }
        $gets['select'] = 'id,name';
        $modules = table('module')->gets($gets);
        return json_encode($modules);
    }

    function history()
    {
        $this->assign('width', self::$width);
        $this->getInterfaceInfo();
        $this->display('stats/history.php');
    }
}