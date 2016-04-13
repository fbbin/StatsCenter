<?php
$db['master'] = array(
    'type'    => Swoole\Database::TYPE_MYSQLi,
    'host'    => "192.168.1.54",
    'port'    => 3306,
    'dbms'    => 'mysql',
    'engine'  => 'MyISAM',
    'user'    => "log_center",
    'passwd'  => "logdb@123cl",
    'name'    => "log_center",
    'charset' => "utf8",
    'setname' => true,
);
return $db;