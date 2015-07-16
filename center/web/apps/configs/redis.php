<?php
$redis['master'] = array(
    'host' => "localhost",
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

return $redis;