<?php
require __DIR__.'/_init.php';
require "/data/www/public/sdk/autoload.php";
$env = get_cfg_var('env.name');
error_reporting(0);
if ($env == 'dev' or $env == 'test' or $env == 'local')
{
    \Swoole::$php->config->setPath(__DIR__.'/apps/configs/dev/');
    $setting['worker_num'] = 1;
    $setting['daemonize'] = 1;
}
else
{
    \Swoole::$php->config->setPath(__DIR__.'/apps/configs/product/');
    $setting['worker_num'] = 1;
    $setting['daemonize'] = 1;
    $setting['log_file'] = __DIR__ . '/logs/swoole_server.log';
}
$setting['pid_file'] = __DIR__ . '/logs/server.pid';
$svr = new App\Alert();
if ($env == 'dev' or $env == 'test' or $env == 'local')
{
    $svr->setLogger(new Swoole\Log\EchoLog(array('display'=>1)));
}
else
{
    $svr->setLogger(new Swoole\Log\EchoLog(array('display'=>1)));
    //$svr->setLogger(new Swoole\Log\FileLog(__DIR__ . '/logs/server.log'));
}

$svr->run($setting);
