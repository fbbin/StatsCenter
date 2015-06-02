<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
define('WEBROOT', 'http://'.$_SERVER['HTTP_HOST']);

require __DIR__.'/../../framework/libs/lib_config.php';

if (get_cfg_var('env.name') == 'local' or get_cfg_var('env.name') == 'dev')
{
    Swoole::$php->config->setPath(__DIR__.'/apps/configs/dev/');
}
else
{
    Swoole::$php->config->setPath(__DIR__.'/apps/configs/');
}

Swoole::getInstance()->runMVC();
