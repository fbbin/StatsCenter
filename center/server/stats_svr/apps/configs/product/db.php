<?php
$db['master'] = array(
    'type'    => Swoole\Database::TYPE_MYSQLi,
    'host'    => "192.168.1.102",
    'port'    => 3306,
    'dbms'    => 'mysql',
    'engine'  => 'MyISAM',
    'user'    => "mostat",
    'passwd'  => "bufferme",
    'name'    => "mostat",
    'charset' => "utf8",
    'setname' => true,
);
return $db;