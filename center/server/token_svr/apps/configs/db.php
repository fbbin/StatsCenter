<?php
$db['master'] = array(
    'type'    => Swoole\Database::TYPE_MYSQLi,
    'host'    => "192.168.1.254",
    'port'    => 3306,
    'dbms'    => 'mysql',
    'engine'  => 'MyISAM',
    'user'    => "chelun",
    'passwd'  => "qlEROymKIKwf",
    'name'    => "app_chelun",
    'charset' => "utf8",
    'setname' => true,
);
return $db;
