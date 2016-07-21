<?php
ini_set("memory_limit","1024M");
define('SWOOLE_SERVER', true);
require_once dirname(__DIR__).'/config.php';

$start = microtime(true);
echo("[" . date("Y-m-d H:i:s") . "] start to sum \n");
//$i_gets['select'] = 'id,name';
//$i_gets['order'] = 'id asc';
//$project_info = table('project', 'platform')->getMap($i_gets,'name' );

//Swoole::$php->db->debug = true;

$date = empty($argv[1]) ? '' : $argv[1];
$program = new StatsSum($date);
$program->sum();

$end = microtime(true);
echo("[" . date("Y-m-d H:i:s") . "] end report \n");
$elapsed = $end - $start;
echo("[" . date("Y-m-d H:i:s") . "] elapsed time $elapsed s\n");

class StatsSum
{
    protected $date;
    protected $moduleInfo;

    function __construct($date = null)
    {
        if (!$date)
        {
            $this->date = date('Ymd');
        }
        else
        {
            $this->date = $date;
        }
    }

    function createTable($table)
    {
        $sql1 = "CREATE TABLE IF NOT EXISTS `" . $table . "` (
  `interface_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `interface_name` varchar(128) NOT NULL,
  `module_name` varchar(128) NOT NULL,
  `total_count` int(11) NOT NULL,
  `fail_count` int(11) NOT NULL,
  `succ_count` int(11) NOT NULL,
  `total_time` double NOT NULL,
  `total_fail_time` double NOT NULL,
  `max_time` double NOT NULL,
  `min_time` double NOT NULL,
  `avg_time` double NOT NULL,
  `succ_rate` float NOT NULL,
  `avg_fail_time` double NOT NULL,
  PRIMARY KEY (`interface_id`,`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        Swoole::$php->db->query($sql1);

        $sql2 = "ALTER TABLE `{$table}` ADD INDEX( `module_id`);";
        Swoole::$php->db->query($sql2);
    }

    /**
     * 汇总接口的数据
     * @param $interface_id
     * @param $name
     * @param $module_info
     * @return bool|int
     * @throws Exception
     */
    function sumInterfaceData($ifce, $module_info)
    {
        $interface_id = $ifce['id'];
        $name = $ifce['name'];
        $module_id = $ifce['module_id'];
        $today = $this->date;
        $table = "stats_" . $today;
        $sum_table = table('stats_sum_' . $today);

        //$table = "stats_20150818";
        $gets['order'] = 'time_key asc';
        $gets['interface_id'] = $interface_id;
        if (!empty($module_id))
        {
            $gets['module_id'] = $module_id;
        }
        $res = table($table)->gets($gets);
        if (!empty($res))
        {
            $caculate = array();
            foreach ($res as $v)
            {
                //基础接口信息
                if (!isset($caculate['interface_id']))
                {
                    $caculate['interface_id'] = $v['interface_id'];
                }
                //基础模块信息
                if (!isset($caculate['module_id']))
                {
                    $caculate['module_id'] = $v['module_id'];
                }
                //总数
                if (!isset($caculate['total_count']))
                {
                    $caculate['total_count'] = $v['total_count'];
                }
                else
                {
                    $caculate['total_count'] += $v['total_count'];
                }
                //失败汇总
                if (!isset($caculate['fail_count']))
                {
                    $caculate['fail_count'] = $v['fail_count'];
                }
                else
                {
                    $caculate['fail_count'] += $v['fail_count'];
                }
                //总时间汇总
                if (!isset($caculate['total_time']))
                {
                    $caculate['total_time'] = $v['total_time'];
                }
                else
                {
                    $caculate['total_time'] += $v['total_time'];
                }
                //总失败时间汇总 total_fail_time
                if (!isset($caculate['total_fail_time']))
                {
                    $caculate['total_fail_time'] = $v['total_fail_time'];
                }
                else
                {
                    $caculate['total_fail_time'] += $v['total_fail_time'];
                }

                //获取最大时间
                if (!isset($caculate['max_time']))
                {
                    $caculate['max_time'] = $v['max_time'];
                }
                elseif($caculate['max_time'] < $v['max_time'])
                {
                    $caculate['max_time'] = $v['max_time'];
                }
                //获取最小时间
                if (!isset($caculate['min_time']))
                {
                    $caculate['min_time'] = $v['min_time'];
                }
                elseif($caculate['min_time'] > $v['min_time'])
                {
                    $caculate['min_time'] = $v['min_time'];
                }
            }

            //平均响应时间
            if ($caculate['total_count'] != 0)
            {
                $caculate['avg_time'] = number_format($caculate['total_time'] / $caculate['total_count'], 2);
                $caculate['succ_rate'] = floor((($caculate['total_count'] - $caculate['fail_count']) / $caculate['total_count']) * 10000) / 100;
            }
            else
            {
                $caculate['avg_time'] = 0;
                $caculate['succ_rate'] = 0;
            }
            //平均失败响应时间
            if ($caculate['fail_count'] != 0)
            {
                $caculate['avg_fail_time'] = number_format($caculate['total_fail_time'] / $caculate['fail_count'],2);
            }
            else
            {
                $caculate['avg_fail_time'] = 0;
            }
            $caculate['succ_count'] = $caculate['total_count'] - $caculate['fail_count'];
            $caculate['interface_name'] = $name;
            $module_name = isset($module_info[$caculate['module_id']]) ? $module_info[$caculate['module_id']] : '';
            $caculate['module_name'] = $module_name;

            //interface_id + module_id
            $primary_key = [
                'interface_id' => $interface_id,
                'module_id' => $caculate['module_id'],
            ];

            $exist = $sum_table->exists($primary_key);
            //不存在
            if (!$exist)
            {
                if (!$sum_table->put($caculate) and \Swoole::$php->db->errno() == 1146)
                {
                    $this->createTable($sum_table->table);
                    return $sum_table->put($caculate);
                }
            }
            else
            {
                unset($caculate['interface_id'], $caculate['module_id'], $caculate['interface_name'], $caculate['module_name']);
                return $sum_table->sets($caculate, $primary_key);
            }
        }
        else
        {
            return false;
        }
    }

    function getLockFile()
    {
        return __DIR__ . '/' . $this->date . '.sum.lock';
    }

    function getModuleInfo()
    {
        $u_gets['select'] = "id, username";
        $m_gets['select'] = 'id, name';
        $this->moduleInfo = table("module")->getMap($m_gets, 'name');
    }

    function sum()
    {
        $lock_file = $this->getLockFile();
        if (is_file($lock_file))
        {
            die("StatsSum program is running.");
        }
        file_put_contents($lock_file, 'locked');
        //获取所有接口
        $i_gets['select'] = 'id, name, module_id';
        $i_gets['order'] = 'id desc';
        $interface_info = table("interface")->gets($i_gets);

        $this->getModuleInfo();

        //工作进程数量
        $worker_num = 10;
        $worker_pool = array();
        $pagesize = ceil(count($interface_info) / $worker_num);

        for ($i = 0; $i < $worker_num; $i++)
        {
            $interfaces = array_slice($interface_info, $i * $pagesize, $pagesize);
            $process = new swoole_process(function($o) use ($interfaces, $i) {
                echo "worker#{$i}, interface_num=".count($interfaces)." start\n";
                Swoole::getInstance()->db->close();
                Swoole::getInstance()->db->connect();
                foreach ($interfaces as $ifce)
                {
                    $res = $this->sumInterfaceData($ifce, $this->moduleInfo);
                    if ($res)
                    {
                        Swoole\Filter::safe($ifce['name']);
                        echo "update interface [".$ifce['name']."] success\n";
                    }
                }
            }, false, false);
            $process->start();
            $worker_pool[] = $process;
        }
        for ($i = 0; $i < $worker_num; $i++)
        {
            swoole_process::wait();
        }
        unlink($lock_file);
    }
}
