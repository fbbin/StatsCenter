<?php
require __DIR__.'/_init.php';

class Test
{
    public $msg;
    public $alert;
    const SMS_INTERFACE_KEY = "platform/captcha_sms_interface";
    const SMS_CHANNEL_KEY = "platform/sms";
    const ENV = "test";
    static $sms_interface = array();
    static $sms_channel = array();

    function __construct()
    {
        $this->load_sms_config();
    }

    function load_sms_config()
    {
        //加载短信权重配置 短信验证码接口ID配置
        $sms_interface_path = "/data/config/" . self::SMS_INTERFACE_KEY . ".conf";
        $tmp_sms_interface = json_decode(file_get_contents($sms_interface_path), 1);
        if (!empty($tmp_sms_interface)) {
            self::$sms_interface = $tmp_sms_interface;
        }

        $sms_channel_path = "/data/config/" . self::SMS_CHANNEL_KEY . ".conf";
        $tmp_sms_channel = json_decode(file_get_contents($sms_channel_path), 1);
        if ($tmp_sms_channel) {
            self::$sms_channel = $tmp_sms_channel;
        }
    }

    function repush_sms_config($interface)
    {
        $interface_id = $interface['interface_id'];
        if (empty($interface_id)) {
            return false;
        }

        $used = self::$sms_interface[$interface_id]['use'];
        $channel_id = self::$sms_interface[$interface_id]['id'];
        if (empty($used)) {
            return false;
        }

        $old_config = self::$sms_channel;
        $sms_channel = self::$sms_channel;
        if (empty($sms_channel)) {
            return false;
        }
        //该接口全部失败 将权重设置为0
        $need_push = false;
        foreach ($used as $use) {
            if (!empty($sms_channel['weight'][$use][$channel_id])) {
                $weight = $sms_channel['weight'][$use][$channel_id]['weight'];
                //无效的通道配置不重新推送
                \Swoole::$php->log->trace("{$use} name {$sms_channel['weight'][$use][$channel_id]['name']} weight:{$sms_channel['weight'][$use][$channel_id]['weight']}");
                if ($weight > 0) {
                    $sms_channel['weight'][$use][$channel_id]['weight'] = 0;
                    //遍历该功能下所有渠道，将失败的接口权重 转移至有效的渠道中
                    foreach ($sms_channel['weight'][$use] as $id => $channel) {
                        if (($id != $channel_id) and $channel['weight'] > 0) {
                            $sms_channel['weight'][$use][$id]['weight'] += $weight;
                            $need_push = true;
                            break;
                        }
                    }
                }
            }
        }
        if ($need_push) {
            //重新加载一次配置 给下一次请求使用
            $this->load_sms_config();
            $new_sms_config = json_encode($sms_channel);
            $curl = new \Swoole\Client\CURL();
            $url = "http://cc.oa.com/api/modify_config/";
            $data = array(
                'env' => self::ENV,
                'ckey' => self::SMS_CHANNEL_KEY,
                'config_data' => $new_sms_config,
            );
            $res = $curl->post($url, $data);
            unset($curl);
            \Swoole::$php->log->trace("repush sms old_config:" . json_encode($old_config) . " new_config:" . $new_sms_config . " push res: " . $res);
        } else {
            \Swoole::$php->log->trace("no need push");
        }
        return false;
    }
}

$t = new Test();
$interface['interface_id'] = '5324160';
$t->repush_sms_config($interface);