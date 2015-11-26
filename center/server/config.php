<?php
define('PUBLIC_PATH', '/data/www/public/');
define('FRAMEWORK_PATH', PUBLIC_PATH.'/framework');
define('ENV_NAME', $env);

require FRAMEWORK_PATH . '/libs/lib_config.php';
Swoole\Loader::addNameSpace('StatsCenter', __DIR__ . '/classes');

global $php;
$php = Swoole::getInstance();

$env = get_cfg_var('env.name');
if ($env == 'dev' or $env == 'test' or $env == 'local')
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/dev');
}
else
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/product');
}
