<?php
$db['master'] = array(
    'type'    => Swoole\Database::TYPE_MYSQL,
    'host'    => "bufferme",
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