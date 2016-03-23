<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);

require dirname(__DIR__) . '/config.php';
Swoole::$php->config->setPath(__DIR__.'/configs/');

class Cron
{
    public $jobs;
    public $times;

    public function set_job($name)
    {
        $this->jobs[] = $name;
    }

    function log($log)
    {
        echo "[".date("Y-m-d H:i:s")."] ".$log.PHP_EOL;
    }

    function time_mark($key)
    {
        $this->times[$key][] = microtime(true);
    }
    function start($name)
    {
        $this->log("start running:$name");
        $this->time_mark($name);
        $name();
        $this->end($name);
    }
    function end($name)
    {
        $this->log("end running:$name");
        $this->time_mark($name);
        $this->log("elapsed:".($this->times[$name][1] - $this->times[$name][0]));
    }
    function run()
    {
        if (!empty($this->jobs))
        {
            foreach ($this->jobs as $name)
            {
                $this->start($name);
            }
        }
        else
        {
            $this->log("job list is empty");
        }
    }
}

/**
 * 删除一个月前的日志
 */
function del_log()
{
    $last = date("Ymd", strtotime('-3 month'));
    $sql = "show tables like 'logs2_%'";
    $res = Swoole::$php->db->query($sql)->fetchall();
    if (!empty($res))
    {
        foreach ($res as $re)
        {
            list($table_name) = array_values($re);
            list(, $date) = explode('_', $table_name);
            if ($date < $last)
            {
                dropTable($table_name);
                echo "drop table $table_name success\n";
            }
        }
    }
    else
    {
        echo "no table to delete ".PHP_EOL;
    }
}

/**
 * 删除一个月前的统计数据
 */
function del_stats()
{
    $last = date("Ymd", time() - 3600 * 24 * 30);
    $sql = "show tables like 'stats_client_%'";
    $res = Swoole::$php->db->query($sql)->fetchall();
    if (!empty($res))
    {
        foreach ($res as $re)
        {
            list($table_name) = array_values($re);
            list(, , $date) = explode('_', $table_name);
            if ($date < $last)
            {
                dropTable('stats_client_' . $date);
                dropTable('stats_server_' . $date);
                dropTable('stats_sum_' . $date);
                dropTable('stats_' . $date);
            }
        }
    }
    else
    {
        echo "no table to delete ".PHP_EOL;
    }
}

function dropTable($table)
{
    $sql = "DROP TABLE IF EXISTS `$table`";
    $del_res = Swoole::$php->db->query($sql);
    echo $sql;
    if ($del_res)
    {
        echo ' success ' . PHP_EOL;
    }
    else
    {
        echo ' failed ' . PHP_EOL;
    }
}

$cron = new Cron();
$cron->set_job('del_log');
$cron->set_job('del_stats');
$cron->run();
