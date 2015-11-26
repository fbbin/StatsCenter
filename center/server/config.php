<?php
define('PUBLIC_PATH', '/data/www/public/');
define('FRAMEWORK_PATH', PUBLIC_PATH.'/framework');

$env = get_cfg_var('env.name');
$env = empty($env) ? 'product' : $env;
define('ENV_NAME', $env);

require FRAMEWORK_PATH . '/libs/lib_config.php';
Swoole\Loader::addNameSpace('StatsCenter', __DIR__ . '/classes');

if (ENV_NAME == 'dev' or ENV_NAME == 'test' or ENV_NAME == 'local')
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/dev');
}
else
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/product');
}
