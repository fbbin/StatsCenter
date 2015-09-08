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
    //\Swoole\Error::dbd();
    $now = date("Y-m-d",time()-3600*24*30);
    $sql = "show tables like 'logs2_%'";
    $res = Swoole::$php->db->query($sql)->fetchall();
    if (!empty($res))
    {
        foreach ($res as $re)
        {
            foreach ($re as $r)
            {
                $tmp = explode('_',$r);
                if ($tmp[1] < $now)
                {
                    $sql = "DROP TABLE IF EXISTS `$r`";
                    $res = Swoole::$php->db->query($sql);
                    echo $sql;
                    if ($res)
                    {
                        echo ' success '.PHP_EOL;
                    }
                    else
                    {
                        echo ' failed '.PHP_EOL;
                    }

                }
            }
        }
    }
    else
    {
        echo "no table to delete ".PHP_EOL;
    }
}

/**
 * 删除一个月前的日志
 */
function del_stats()
{
    $last = date("Y-m-d", time() - 3600 * 24 * 30);
    $sql = "show tables like 'stats_server_%'";
    $res = Swoole::$php->db->query($sql)->fetchall();
    if (!empty($res))
    {
        foreach ($res as $re)
        {
            list($table_name) = $re;
            list(, $date) = explode('-', $table_name);

            if ($date < $last)
            {
                $sql = "DROP TABLE IF EXISTS `$table_name`";
                //$res = Swoole::$php->db->query($sql);
                echo $sql;
                if ($res)
                {
                    echo ' success ' . PHP_EOL;
                }
                else
                {
                    echo ' failed ' . PHP_EOL;
                }

            }
        }
    }
    else
    {
        echo "no table to delete ".PHP_EOL;
    }
}

$cron = new Cron();
//$cron->set_job('del_log');
$cron->set_job('del_stats');
$cron->run();
