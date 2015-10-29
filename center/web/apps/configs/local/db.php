<?php
$db['master'] = array(
    'type'    => Swoole\Database::TYPE_MYSQL,
    'host'    => "127.0.0.1",
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
    'host'    => "10.10.2.38",
    'port'    => 3306,
    'dbms'    => 'mysql',
    'engine'  => 'MyISAM',
    'user'    => "root",
    'passwd'  => "root",
    'name'    => "platform",
    'charset' => "utf8",
    'setname' => true,
);

return $db;