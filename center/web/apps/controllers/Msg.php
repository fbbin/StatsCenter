<?php
namespace App\Controller;
use Swoole;
use App;

class Msg extends \App\LoginController
{
    static $channel = array(
        0 => '全部',
        1 => '亿美',
        2 => '漫道',
        3 => '梦网',
    );

    static $msg_type = array(
        0 => '全部',
        1 => '文本',
        2 => '语音',
    );
    function msg_stats()
    {
        if (!empty($_POST['start_time']))
        {
            $gets['where'][] = 'addtime >= "'.trim($_POST['start_time']).'"';
        } else {
            $gets['where'][] = 'addtime >= "'.date("Y-m-d H:i:s",time()-3600*24).'"';
        }
        if (!empty($_POST['end_time']))
        {
            $gets['where'][] = 'addtime <= "'.trim($_POST['end_time']).'"';
        } else {
            $gets['where'][] = 'addtime >= "'.date("Y-m-d H:i:s").'"';
        }
        //\Swoole::$php->db("platform")->debug = 1;

        $gets['order'] = 'id desc';
        $data = table("sms_log","platform")->gets($gets);
        $calc = array();
        foreach ($data as $k => $d)
        {
            if (!isset($calc[0]['count'])) {
                $calc[0]['count'] = 1;
            } else {
                $calc[0]['count'] ++;
            }
            if (!isset($calc[$d['channel']]['count'])) {
                $calc[$d['channel']]['count'] = 1;
            } else {
                $calc[$d['channel']]['count'] ++;
            }

            if ($d['success'] == 0) {
                if (!isset($calc[0]['success'])) {
                    $calc[0]['success'] = 1;
                } else {
                    $calc[0]['success'] ++;
                }
                if (!isset($calc[$d['channel']]['success'])) {
                    $calc[$d['channel']]['success'] = 1;
                } else {
                    $calc[$d['channel']]['success'] ++;
                }
            } else {
                if (!isset($calc[0]['failed'])) {
                    $calc[0]['failed'] = 1;
                } else {
                    $calc[0]['failed'] ++;
                }
                if (!isset($calc[$d['channel']]['failed'])) {
                    $calc[$d['channel']]['failed'] = 1;
                } else {
                    $calc[$d['channel']]['failed'] ++;
                }
            }
        }

        foreach ($calc as $k => $v)
        {
            $calc[$k]['name'] = self::$channel[$k];
            $calc[$k]['success_rate'] = number_format(($v['success']/$v['count'])*100,2);
            $calc[$k]['failed_rate'] = number_format(($v['failed']/$v['count'])*100,2);
        }
        $this->assign('data', $calc);
        $this->display();
    }

    function captcha_stats()
    {
        if (!empty($_POST['start_time']))
        {
            $gets['where'][] = 'add_time >= "'.strtotime(trim($_POST['start_time'])).'"';
        } else {
            $gets['where'][] = 'add_time >= "'.(time()-3600*24).'"';
        }
        if (!empty($_POST['end_time']))
        {
            $gets['where'][] = 'add_time <= "'.(time()).'"';
        }

        $type = 0;
        if (!empty($_POST['type']))
        {
            $gets['type'] = $_POST['type'];
            $type = $_POST['type'];
        }

            //\Swoole::$php->db("platform")->debug = 1;

        $gets['order'] = 'id desc';
        $data = table("msg_captcha_log","platform")->gets($gets);
        $calc = array();
        foreach ($data as $k => $d)
        {
            if (!isset($calc[0]['count'])) {
                $calc[0]['count'] = 1;
            } else {
                $calc[0]['count'] ++;
            }

            if (!isset($calc[$d['channel']]['count'])) {
                $calc[$d['channel']]['count'] = 1;
            } else {
                $calc[$d['channel']]['count'] ++;
            }

            if ($d['is_used'] == 0) {
                if (!isset($calc[0]['no'])) {
                    $calc[0]['no'] = 1;
                } else {
                    $calc[0]['no'] ++;
                }
                if (!isset($calc[$d['channel']]['no'])) {
                    $calc[$d['channel']]['no'] = 1;
                } else {
                    $calc[$d['channel']]['no'] ++;
                }
            } else {
                if (!isset($calc[0]['used'])) {
                    $calc[0]['used'] = 1;
                } else {
                    $calc[0]['used'] ++;
                }
                if (!isset($calc[$d['channel']]['used'])) {
                    $calc[$d['channel']]['used'] = 1;
                } else {
                    $calc[$d['channel']]['used'] ++;
                }
            }
        }

        foreach ($calc as $k => $v)
        {
            $calc[$k]['type'] = self::$msg_type[$type];
            $calc[$k]['name'] = self::$channel[$k];
            $calc[$k]['used_rate'] = number_format(($v['used']/$v['count'])*100,2);
        }
        $this->assign('data', $calc);
        $this->assign('type', self::$msg_type);
        $this->display();
    }

    function smslog()
    {
//        $this->db('platform')->debug = true;
        $gets["order"] = 'id desc';
        $gets['page'] = !empty($_GET['page']) ? $_GET['page'] : 1;
        $gets['pagesize'] = 20;

        if (empty($_GET['date']))
        {
            $_GET['date'] = date('Y-m-d');
        }

        $gets['where'][] = 'addtime >= "'.$_GET['date'].' 00:00:00'.'"';
        $gets['where'][] = 'addtime <= "'.$_GET['date'].' 23:59:59'.'"';

        if (!empty($_GET['mobile']))
        {
            $gets['mobile'] = intval($_GET['mobile']);
        }

        $data = table('sms_log', 'platform')->gets($gets, $pager);
        $this->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
        $this->assign('data', $data);
        $this->display();
    }
}