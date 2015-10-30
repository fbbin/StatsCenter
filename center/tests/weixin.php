<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);

require __DIR__.'/../../framework/libs/lib_config.php';

if (get_cfg_var('env.name') == 'local' or get_cfg_var('env.name') == 'dev')
{
    Swoole::$php->config->setPath(__DIR__.'/apps/configs/dev/');
}

$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=wxc45d2ffe103e99c1&corpsecret=7T5TQbFHCYT5J2Z23qPKH3OaefjAIdO3FJjcap_28KUUFAbI0exS5lL4yI2fHKp1";
$ch = new \Swoole\Client\CURL();
$res = $ch->get($url);
$t = json_decode($res,1);
$token = $t['access_token'];
$msg_url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$token}";
$msg = array(
    'touser' => 'shiguangqi|chendongxiong',
    'toparty' => '',
    'totag' => '',
    'msgtype' => 'text',
    'agentid' => 0,
    'text' => array(
        'content' => '单独发送11111',
    ),
    'safe' => 0
);
$res = $ch->post($msg_url,json_encode($msg,JSON_UNESCAPED_UNICODE));
debug($res);
