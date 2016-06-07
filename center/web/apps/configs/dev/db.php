<?php
$db['master'] = array(
	'type'    => Swoole\Database::TYPE_MYSQLi,
	'host'    => "localhost",
	'port'    => 3306,
	'dbms'    => 'mysql',
	'engine'  => 'MyISAM',
	'user'    => "root",
	'passwd'  => "root",
	'name'    => "mostat",
	'charset' => "utf8",
	'setname' => true,
);

$db['platform'] = array(
	'type'    => Swoole\Database::TYPE_MYSQLi,
	'host'    => "localhost",
	'port'    => 3306,
	'dbms'    => 'mysql',
	'engine'  => 'MyISAM',
	'user'    => "root",
	'passwd'  => "root",
	'name'    => "mostat",
	'charset' => "utf8",
	'setname' => true,
);

$db['log_center'] = array(
	'type'    => Swoole\Database::TYPE_MYSQLi,
	'host'    => "localhost",
	'port'    => 3306,
	'dbms'    => 'mysql',
	'engine'  => 'MyISAM',
	'user'    => "root",
	'passwd'  => "root",
	'name'    => "states",
	'charset' => "utf8",
	'setname' => true,
);

$db['app_stats'] = array(
	'type'    => Swoole\Database::TYPE_MYSQLi,
	'host'    => "localhost",
	'port'    => 3306,
	'dbms'    => 'mysql',
	'engine'  => 'MyISAM',
	'user'    => "root",
	'passwd'  => "root",
	'name'    => "states",
	'charset' => "utf8",
	'setname' => true,
);
return $db;