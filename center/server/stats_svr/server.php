<?php
require __DIR__.'/_init.php';

$env = get_cfg_var('env.name');
if ($env == 'dev' or $env == 'test' or $env == 'local')
{
    $worker_num = 1;
    $setting['daemonize'] = 1;
}
else
{
    $worker_num = 24;
    $setting['daemonize'] = true;
}

$setting['worker_num'] = $worker_num;
$setting['task_worker_num'] = $worker_num;

$setting['pid_file'] = __DIR__ . '/logs/server.pid';
$setting['worker_dump_file'] = __DIR__ . '/dump/worker';
$setting['task_dump_file'] = __DIR__ . '/dump/task';

$logger = new Swoole\Log\FileLog(__DIR__ . '/logs/server.log');
$svr = new StatsCenter\StatsServer();
$svr->setLogger($logger);

$svr->run($setting);

