<?php
require __DIR__.'/../../framework/libs/lib_config.php';
Swoole\Loader::addNameSpace('StatsCenter', __DIR__ . '/classes');

global $php;
$php = Swoole::getInstance();

if (get_cfg_var('env.name') == 'local' or get_cfg_var('env.name') == 'dev')
{
    $php->config->setPath(__DIR__.'/apps/configs/dev/');
}
