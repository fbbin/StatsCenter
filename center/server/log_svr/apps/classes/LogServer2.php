<?php
namespace App;
use StatsCenter;
use Swoole\Filter;

class LogServer2 extends StatsCenter\Server
{
    const PORT = 9905;
    const EOF  = "\r\n";

    function __construct()
    {
        if (get_cfg_var('env.name') == 'local' or get_cfg_var('env.name') == 'dev')
        {
            \Swoole::$php->config->setPath(WEBPATH . '/apps/configs/dev/');
        }
    }

    function run($_setting = array())
    {
        $default_setting = array(
            'dispatch_mode' => 3,
            'max_request' => 0,
            'open_eof_split' => true,
            'package_eof' => self::EOF,
        );

        define('SWOOLE_SERVER', true);
        $setting = array_merge($default_setting, $_setting);
        $serv = new \swoole_server('0.0.0.0', self::PORT, SWOOLE_PROCESS);
        $serv->set($setting);
        $serv->on('receive', array($this, 'onReceive'));
        $this->serv = $serv;
        $this->serv->start();
    }

    function onReceive($serv, $fd, $thread_id, $data)
    {
        $info = $this->serv->connection_info($fd, $thread_id, true);
        $parts = explode("\n", $data, 2);
        $put = array();

        list($put['module'], $put['level'], $put['type'], $put['subtype'], $put['uid']) = explode("|", $parts[0]);
        $put['content'] = Filter::escape(rtrim($parts[1]));
        $put['hour'] = date('H');
        $put['ip'] = $info['remote_ip'];
        if (!is_numeric($put['uid']))
        {
            $put['ukey'] = $put['uid'];
            $put['uid'] = 0;
        }

        $table = $this->getTableName();
        if (!table($table)->put($put) and \Swoole::$php->db->errno() == 1146)
        {
            $this->createTable($table);
            table($table)->put($put);
        }

        if (!empty($put['ip']))
        {
            \Swoole::$php->redis->sAdd('logs2:client:' . $put['module'], $put['ip']);
        }
        if (!empty($put['type']))
        {
            \Swoole::$php->redis->sAdd('logs2:type:' . $put['module'], $put['type']);
        }
        if (!empty($put['subtype']))
        {
            \Swoole::$php->redis->sAdd('logs2:subtype:' . $put['module'], $put['subtype']);
        }
    }

    /**
     * 获取表名， logs_日期
     */
    protected function getTableName()
    {
        return 'logs2_'.date('Ymd');
    }

    /**
     * 按日期分表
     * @param $table
     */
    protected function createTable($table)
    {
        $create_table_sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `module` int(11) NOT NULL,
  `type` varchar(40) NOT NULL,
  `subtype` varchar(40) NOT NULL,
  `uid` int(11) NOT NULL,
  `ukey` varchar(128) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `content` text NOT NULL,
  `ip` varchar(40) NOT NULL,
  `hour` tinyint(4) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $create_index_sql = "ALTER TABLE `{$table}` ADD INDEX( `type`, `subtype`, `ip`);";
        $create_index_sql2 = "ALTER TABLE `{$table}` ADD INDEX( `uid`);";
        $r = \Swoole::$php->db->query($create_table_sql);
        //创建表成功后再建索引，避免重复创建索引
        if ($r)
        {
            \Swoole::$php->db->query($create_index_sql);
            \Swoole::$php->db->query($create_index_sql2);
        }
    }
}