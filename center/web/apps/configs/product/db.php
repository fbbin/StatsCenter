<?php
/*$db['master'] = array(
    'type' => Swoole\Database::TYPE_CLMysql,
    'host' => "192.168.1.105",
    'port' => 9703,
    'dbms' => 'mysql',
    'engine' => 'MyISAM',
    'user' => "mostat",
    'passwd' => "bufferme",
    'name' => "mostat",
    'charset' => "utf8",
    'setname' => true,
    'persistent' => true,
);

$db['platform'] = array(
    'type' => Swoole\Database::TYPE_CLMysql,
    'host' => "192.168.1.105",
    'port' => 9703,
    'dbms' => 'mysql',
    'engine' => 'MyISAM',
    'user' => "platform",
    'passwd' => "Emuo0koo",
    'name' => "platform",
    'charset' => "utf8",
    'setname' => true,
    'persistent' => true,
);

$db['log_center'] = array(
    'type' => Swoole\Database::TYPE_CLMysql,
    'host' => "192.168.1.105",
    'port' => 9703,
    'dbms' => 'mysql',
    'engine' => 'MyISAM',
    'user' => "log_center",
    'passwd' => "logdb@123cl",
    'name' => "log_center",
    'charset' => "utf8",
    'setname' => true,
    'persistent' => true,
);*/

$db['master'] = array(
	'type' => Swoole\Database::TYPE_MYSQLi,
	'host' => "192.168.1.102",
	'port' => 3306,
	'dbms' => 'mysql',
	'engine' => 'MyISAM',
	'user' => "mostat",
	'passwd' => "bufferme",
	'name' => "mostat",
	'charset' => "utf8",
	'setname' => true,
);

$db['platform'] = array(
	'type' => Swoole\Database::TYPE_MYSQLi,
	'host' => "192.168.1.212",
	'port' => 3306,
	'dbms' => 'mysql',
	'engine' => 'MyISAM',
	'user' => "platform",
	'passwd' => "Emuo0koo",
	'name' => "platform",
	'charset' => "utf8",
	'setname' => true,
);

$db['log_center'] = array(
	'type' => Swoole\Database::TYPE_MYSQLi,
	'host' => "192.168.1.54",
	'port' => 3306,
	'dbms' => 'mysql',
	'engine' => 'MyISAM',
	'user' => "log_center",
	'passwd' => "logdb@123cl",
	'name' => "log_center",
	'charset' => "utf8",
	'setname' => true,
);

return $db;