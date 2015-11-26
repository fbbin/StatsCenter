#! /usr/bin/php
<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
require dirname(__DIR__) . '/config.php';

$setting['worker_num'] = 16;
$setting['daemonize'] = false;

ini_set('error_log', __DIR__.'/logs/php_errors.log');

$svr = new StatsCenter\AopNetServer();
$svr->setLogger(new Swoole\Log\FileLog(__DIR__.'/logs/server.log'));
$setting['pid_file'] = __DIR__.'/logs/server.pid';
$svr->run($setting);
