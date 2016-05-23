<?php
$redis['master'] = array(
    'host' => "127.0.0.1",
    'port' => 6379,
    'database' => 15,
);

/**
 * Codis集群服务器
 */
$redis['cluster'] = array(
    'host' => "192.168.1.244",
    'port' => 19000,
);

$redis['platform'] = array(
    'host' => '192.168.1.234',
    'port' => 19006,
);

return $redis;
