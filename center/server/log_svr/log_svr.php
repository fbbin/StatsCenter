<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
require dirname(__DIR__) . '/config.php';

$setting['worker_num'] = 8;
//$setting['daemonize'] = 1;

$svr = new StatsCenter\LogServer2();
$svr->setLogger(new Swoole\Log\FileLog(__DIR__ . '/logs/app.log'));
$setting['pid_file'] = __DIR__ . '/logs/server.pid';
$svr->run($setting);
