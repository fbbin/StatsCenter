<?php
$env = get_cfg_var('env.name');
$env = empty($env) ? 'product' : $env;

define('DEBUG', 'on');
define('WEBPATH', __DIR__);
if (isset($_SERVER['HTTP_HOST']))
{
    define('WEBROOT', 'http://' . $_SERVER['HTTP_HOST']);
}
define('PUBLIC_PATH', '/data/www/public/');
define('FRAMEWORK_PATH', PUBLIC_PATH.'/framework');
define('ENV_NAME', $env);

require FRAMEWORK_PATH . '/libs/lib_config.php';
require __DIR__ . '/apps/include/functions.php';
require __DIR__ . '/apps/include/constants.php';

if (ENV_NAME == 'local')
{
    Swoole::$php->config->setPath(__DIR__ . '/apps/configs/local/');
}
elseif (ENV_NAME == 'dev')
{
    Swoole::$php->config->setPath(__DIR__ . '/apps/configs/dev/');
}
else
{
    Swoole::$php->config->setPath(__DIR__ . '/apps/configs/product');
}
