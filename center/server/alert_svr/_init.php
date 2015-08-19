<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);
require dirname(__DIR__) . '/config.php';
Swoole\Error::$stop = false;
Swoole\Error::$echo_html = false;
Swoole\Error::$display = false;

$env = get_cfg_var('env.name');
if ($env == 'dev' or $env == 'test' or $env == 'local')
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/dev');
}
else
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/product');
}
