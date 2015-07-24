<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
require dirname(__DIR__) . '/config.php';

$setting['worker_num'] = 24;
$setting['task_worker_num'] = 24;
$setting['daemonize'] = false;
$setting['pid_file'] = __DIR__.'/dump/server.pid';
$setting['worker_dump_file'] = __DIR__.'/dump/worker';
$setting['task_dump_file'] = __DIR__.'/dump/task';
$svr = new StatsCenter\StatsServer();
$svr->setLogger(new Swoole\Log\FileLog(__DIR__.'/logs/server.log'));
$svr->run($setting);

