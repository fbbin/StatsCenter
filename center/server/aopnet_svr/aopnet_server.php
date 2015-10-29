#! /usr/bin/php
<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
require dirname(__DIR__) . '/config.php';

$setting['worker_num'] = 4;
$setting['daemonize'] = false;

ini_set('error_log', __DIR__.'/logs/php_errors.log');

if (get_cfg_var('env.name') == 'local' or get_cfg_var('env.name') == 'dev')
{
    Swoole::$php->config->setPath(__DIR__.'/apps/configs/dev/');
}

$svr = new StatsCenter\AopNetServer();
$svr->setLogger(new Swoole\Log\FileLog(__DIR__.'/logs/server.log'));
$setting['pid_file'] = __DIR__.'/logs/server.pid';
$svr->run($setting);
