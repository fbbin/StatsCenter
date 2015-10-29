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
$msg_url = "https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token={$token}";

//"userid": "zhangsan",
//   "name": "张三",
//   "department": [1, 2],
//   "position": "产品经理",
//   "mobile": "15913215421",
//   "gender": "1",
//   "email": "zhangsan@gzdev.com",
//   "weixinid": "zhangsan4dev",
//   "avatar_mediaid": "2-G6nrLmr5EC3MNb_-zL1dDdzkd0p7cNliYu9V5w7o8K0",
//   "extattr": {"attrs":[{"name":"爱好","value":"旅游"},{"name":"卡号","value":"1234567234"}]}
$msg = array(
    'userid' => 'chendongxiong',
    'name' => '陈东雄',
    'department' => array(1),
    'position' => '开发',
    'mobile' => '',
    'gender' => 1,
    'email' => '',
    'weixinid' => "dongxiongchan",
//    "avatar_mediaid"=> "2-G6nrLmr5EC3MNb_-zL1dDdzkd0p7cNliYu9V5w7o8K0"

);
$res = $ch->post($msg_url,json_encode($msg,JSON_UNESCAPED_UNICODE));
debug($res);
