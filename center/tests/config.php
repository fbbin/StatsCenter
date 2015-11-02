<?php
require_once '/data/www/public/sdk/StatsCenter.php';
require_once '/data/www/public/sdk/CloudConfig.php';

StatsCenter::$sc_svr_ip_dev = '127.0.0.1';
StatsCenter::$net_svr_ip_dev = '127.0.0.1';
CloudConfig::$AOPNET_SVR_IP = '127.0.0.1';

$conf = \CloudConfig::getFromCloud('config:category', 'system');
var_dump($conf);
