<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
require dirname(__DIR__) . '/config.php';

$env = get_cfg_var('env.name');
if ($env == 'dev' or $env == 'test' or $env == 'local')
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/dev');
}
else
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/product');
}
