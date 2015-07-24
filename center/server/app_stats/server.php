#! /usr/bin/php
<?php
require __DIR__ . '/_init.php';

$setting['worker_num'] = 4;
//$setting['daemonize'] = 1;

$svr = new StatsCenter\AppStatsServer();
$svr->setLogger(new Swoole\Log\FileLog(__DIR__ . '/logs/app.log'));
$setting['pid_file'] = __DIR__ . '/logs/server.pid';
$svr->run($setting);
