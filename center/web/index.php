<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
require __DIR__.'/../../framework/libs/lib_config.php';

if (get_cfg_var('env.name') == 'local' or get_cfg_var('env.name') == 'dev')
{
    Swoole::$php->config->setPath(__DIR__.'/apps/configs/dev/');
    define('WEBROOT', 'https://local.stats.chelun.com/');
}
else
{
    Swoole::$php->config->setPath(__DIR__.'/apps/configs/');
    define('WEBROOT', 'https://stats.chelun.com');
}

Swoole::getInstance()->runMVC();
