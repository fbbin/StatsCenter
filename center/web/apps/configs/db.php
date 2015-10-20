<?php
$db['master'] = array(
    'type'    => Swoole\Database::TYPE_MYSQLi,
    'host'    => "127.0.0.1",
    'port'    => 3306,
    'dbms'    => 'mysql',
    'engine'  => 'MyISAM',
    'user'    => "root",
    'passwd'  => "bufferme",
    'name'    => "mostat",
    'charset' => "utf8",
    'setname' => true,
);

$db['platform'] = array(
    'type'    => Swoole\Database::TYPE_MYSQLi,
    'host'    => "192.168.1.212",
    'port'    => 3306,
    'dbms'    => 'mysql',
    'engine'  => 'MyISAM',
    'user'    => "platform",
    'passwd'  => "Zae0hoz6",
    'name'    => "platform",
    'charset' => "utf8",
    'setname' => true,
);
return $db;