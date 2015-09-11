<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);

require __DIR__.'/../../framework/libs/lib_config.php';

if (get_cfg_var('env.name') == 'local' or get_cfg_var('env.name') == 'dev')
{
    Swoole::$php->config->setPath(__DIR__.'/apps/configs/dev/');
}


$params['config_id'] = "master";
$params['env'] = "dev";
$params['data'] = json_encode(array(
    'ip' =>'127.0.0.1',
    'port' =>"9901",
));
$c = new \Swoole\Client\CURL();
$url = "http://local.stats.chelun.com/service/reg/";
$res = $c->post($url,$params);
debug($res);
