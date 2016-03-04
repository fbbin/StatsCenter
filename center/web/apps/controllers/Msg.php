<?php
namespace App\Controller;

use Swoole;
use App;

class Msg extends \App\LoginController
{
    const CONFIG_DIR = "/data/config/platform/sms_channel.conf";
    static $sms_config;
    static $channel;
    //通道单条信息费用
    static $charge;
    static $msg_type = array(
        1 => '文本',
        2 => '语音',
    );

    function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->get_config();
    }

    function get_config()
    {
        $config = file_get_contents(self::CONFIG_DIR);
        self::$sms_config = json_decode($config,1);
        foreach (self::$sms_config as $id => $info)
        {
            self::$channel[$id] = $info['name'];
            self::$charge[$id] = $info['price'];
        }
    }

    function msg_stats()
    {
        if (!empty($_GET['start_time'])) {
            $start = trim($_GET['start_time']) . " 00:00:00";
            $end = trim($_GET['start_time']) . " 23:59:59";

            $gets['where'][] = 'addtime >= "' . $start . '"';
            $gets['where'][] = 'addtime <= "' . $end . '"';
            //\Swoole::$php->db("platform")->debug = 1;

            $gets['order'] = 'id desc';
            $data = table("sms_log", "platform")->gets($gets);
            $calc = array();
            $all = array();
            foreach ($data as $k => $d) {
                if (!isset($all['count'])) {
                    $all['count'] = 1;
                } else {
                    $all['count']++;
                }
                if (!isset($calc[$d['channel']]['count'])) {
                    $calc[$d['channel']]['count'] = 1;
                } else {
                    $calc[$d['channel']]['count']++;
                }

                if ($d['success'] == 0) {
                    if (!isset($all['success'])) {
                        $all['success'] = 1;
                    } else {
                        $all['success']++;
                    }
                    if (!isset($calc[$d['channel']]['success'])) {
                        $calc[$d['channel']]['success'] = 1;
                    } else {
                        $calc[$d['channel']]['success']++;
                    }
                } else {
                    if (!isset($all['failed'])) {
                        $all['failed'] = 1;
                    } else {
                        $all['failed']++;
                    }
                    if (!isset($calc[$d['channel']]['failed'])) {
                        $calc[$d['channel']]['failed'] = 1;
                    } else {
                        $calc[$d['channel']]['failed']++;
                    }
                }
            }
            foreach ($calc as $k => $v) {
                $calc[$k]['name'] = self::$channel[$k];
                $calc[$k]['success_rate'] = number_format(($v['success'] / $v['count']) * 100, 2);
                $calc[$k]['failed_rate'] = number_format(($v['failed'] / $v['count']) * 100, 2);
            }
            $all['success_rate'] = number_format(($all['success'] / $all['count']) * 100, 2);
            $all['failed_rate'] = number_format(($all['failed'] / $all['count']) * 100, 2);

            $this->assign('data', $calc);
            $this->assign('all', $all);
        }
        $this->display();
    }


    /**
     * 文本验证码使用率按天统计
     * @throws \Exception
     */
    function captcha_stats()
    {
        if (!empty($_GET['start_time'])) {
            $start = trim($_GET['start_time']) . " 00:00:00";
            $end = trim($_GET['start_time']) . " 23:59:59";

            $gets['where'][] = 'add_time >= "' . strtotime($start) . '"';
            $gets['where'][] = 'add_time <= "' . strtotime($end) . '"';

            $type = 0;
            if (!empty($_GET['type']))
            {
                $gets['type'] = (int)$_GET['type'];
                $type = $_GET['type'];
            }

            //\Swoole::$php->db("platform")->debug = 1;

            $gets['order'] = 'id desc';
            $data = table("msg_captcha_log", "platform")->gets($gets);
            //debug($data);
            $calc = array();
            $all = array();
            foreach ($data as $k => $d) {
                if (!isset($all['count'])) {
                    $all['count'] = 1;
                } else {
                    $all['count']++;
                }

                if (!isset($calc[$d['channel']]['count'])) {
                    $calc[$d['channel']]['count'] = 1;
                } else {
                    $calc[$d['channel']]['count']++;
                }

                if ($d['is_used'] == 0) {
                    if (!isset($all['success'])) {
                        $all['no'] = 1;
                    } else {
                        $all['no']++;
                    }
                    if (!isset($calc[$d['channel']]['no'])) {
                        $calc[$d['channel']]['no'] = 1;
                    } else {
                        $calc[$d['channel']]['no']++;
                    }
                } else {
                    if (!isset($all['used'])) {
                        $all['used'] = 1;
                    } else {
                        $all['used']++;
                    }
                    if (!isset($calc[$d['channel']]['used'])) {
                        $calc[$d['channel']]['used'] = 1;
                    } else {
                        $calc[$d['channel']]['used']++;
                    }
                }
            }

            foreach ($calc as $k => $v) {
                $calc[$k]['type'] = self::$msg_type[$type];
                $calc[$k]['name'] = self::$channel[$k];
                $calc[$k]['used_rate'] = number_format(($v['used'] / $v['count']) * 100, 2);
            }
            $all['used_rate'] = number_format(($all['used'] / $all['count']) * 100, 2);
            $this->assign('data', $calc);
            $this->assign('all', $all);
        }
        $this->assign('type', self::$msg_type);
        $this->display();
    }

    function smslog()
    {
//        $this->db('platform')->debug = true;
        $gets["order"] = 'id desc';
        $gets['page'] = !empty($_GET['page']) ? $_GET['page'] : 1;
        $gets['pagesize'] = 20;

        if (empty($_GET['date'])) {
            $_GET['date'] = date('Y-m-d');
        }

        $gets['where'][] = 'addtime >= "' . $_GET['date'] . ' 00:00:00' . '"';
        $gets['where'][] = 'addtime <= "' . $_GET['date'] . ' 23:59:59' . '"';

        if (!empty($_GET['mobile'])) {
            $gets['mobile'] = intval($_GET['mobile']);
        }

        $data = table('sms_log', 'platform')->gets($gets, $pager);
        $this->assign('pager', array('total' => $pager->total, 'render' => $pager->render()));
        $this->assign('data', $data);
        $this->assign('channel', self::$channel);
        $this->display();
    }

    function msg_dis()
    {
        if (!empty($_GET['start_time']) and !empty($_GET['end_time'])) {
            //\Swoole::$php->db("platform")->debug = 1;
            $start_date = trim($_GET['start_time']);
            $end_date = trim($_GET['end_time']);

            $time = array();
            while ($start_date <= $end_date) {
                $time[] = $start_date;
                $start_date = date("Y-m-d", strtotime("$start_date +1 day"));
            }
            if (count($time) > 3) {
                \Swoole\JS::js_goto("最多选择3天时间跨度，请重新选择","/msg/msg_dis");
            }
            $start = trim($_GET['start_time']) . " 00:00:00";
            $end = trim($_GET['end_time']) . " 23:59:59";
            $gets['where'][] = 'addtime >= "' . $start . '"';
            $gets['where'][] = 'addtime <= "' . $end . '"';
            $gets['select'] = 'channel,addtime';
            $gets['order'] = 'id asc';
            $data = table("sms_log", "platform")->gets($gets);
            $sms_log = array();
            $x_sms = array();
            foreach ($data as $k => $d) {
                $day = substr($d['addtime'],0,10);
                if (!isset($sms_log[$day][$d['channel']]['count'])) {
                    $sms_log[$day][$d['channel']]['count'] = 1;
                    $x_sms[] = $day;
                } else {
                    $sms_log[$day][$d['channel']]['count']++;
                }
//                if ($d['success'] == 0) {
//                    if (!isset($sms_log[$day][$d['channel']]['success'])) {
//                        $sms_log[$day][$d['channel']]['success'] = 1;
//                    } else {
//                        $sms_log[$day][$d['channel']]['success']++;
//                    }
//                } else {
//                    if (!isset($sms_log[$day][$d['channel']]['failed'])) {
//                        $sms_log[$day][$d['channel']]['failed'] = 1;
//                    } else {
//                        $sms_log[$day][$d['channel']]['failed']++;
//                    }
//                }
            }
            foreach ($sms_log as $d => $info)
            {
                ksort($info);
                $sms_log[$d] = $info;
            }
            //验证码数据
            $gets = array();
            $gets['where'][] = 'add_time >= "' . strtotime($start) . '"';
            $gets['where'][] = 'add_time <= "' . strtotime($end) . '"';
            $gets['order'] = 'id asc';
            $gets['select'] = 'add_time,channel';
            $data = table("msg_captcha_log", "platform")->gets($gets);
            $captcha_log = array();
            $x_captcha = array();
            foreach ($data as $k => $d) {
                $day = date("Y-m-d",$d['add_time']);
                if (!isset($captcha_log[$day][$d['channel']]['count'])) {
                    $captcha_log[$day][$d['channel']]['count'] = 1;
                    $x_captcha[] = $day;
                } else {
                    $captcha_log[$day][$d['channel']]['count']++;
                }
//                if ($d['success'] == 0) {
//                    if (!isset($captcha_log[$day][$d['channel']]['success'])) {
//                        $captcha_log[$day][$d['channel']]['success'] = 1;
//                    } else {
//                        $captcha_log[$day][$d['channel']]['success']++;
//                    }
//                } else {
//                    if (!isset($captcha_log[$day][$d['channel']]['failed'])) {
//                        $captcha_log[$day][$d['channel']]['failed'] = 1;
//                    } else {
//                        $captcha_log[$day][$d['channel']]['failed']++;
//                    }
//                }
            }
            foreach ($captcha_log as $d => $info)
            {
                ksort($info);
                $captcha_log[$d] = $info;
            }

            $this->assign('time', $time);
            $this->assign('sms', $sms_log);
            $this->assign('captcha', $captcha_log);
            $this->assign('channel', self::$channel);
        }
        $this->display();
    }

    function report()
    {
        if (!empty($_GET['month']) and isset($_GET['channel']) and !empty ($_GET['channel'])) {
            $month = trim($_GET['month']);
            $gets['channel'] = (int)$_GET['channel'];
            if (strval($month)<'2016-03') {
                self::$charge[5] = 0.043;
            }
            $this->assign("price", number_format(self::$charge[$gets['channel']], 3));

            $start = date("Y-m-d H:i:s", strtotime($month));
            $end = date("Y-m-d H:i:s", strtotime("$month +1 month"));
            $gets['where'][] = 'addtime >= "' . $start . '"';
            $gets['where'][] = 'addtime < "' . $end . '"';
            //\Swoole::$php->db("platform")->debug = 1;

            $gets['order'] = 'id desc';
            $gets['group'] = 'days';
            $gets['select'] = "DATE_FORMAT(addtime,'%Y-%m-%d') days,COUNT(id) as c";
            $data = table("sms_log", "platform")->gets($gets);
            $cost = 0;
            $count = 0;
            foreach ($data as $k => $d) {
                if (!empty($d['c'])) {
                    $_cost = $d['c'] * (self::$charge[$gets['channel']]);
                    $data[$k]['cost'] = number_format($_cost, 3);
                    $cost += $_cost;
                    $count += $data[$k]['c'];
                }
            }


            $this->assign('data', $data);

            $this->assign("cost", number_format($cost, 3));
            $this->assign("count", $count);
        }

        $month = $this->getSelect(date("Y-m"), 2);
        $form['channel'] = \Swoole\Form::select('channel', self::$channel, $_GET['channel'], '', array('class' => 'select2'), false);
        $form['month'] = \Swoole\Form::select('month', $month, $_GET['month'], '', array('class' => 'select2'), false);
        $this->assign('form', $form);
        $this->display();
    }

    function dump()
    {
        if (!empty($_GET['month'])) {
            $month = trim($_GET['month']);
        } else {
            \Swoole\JS::js_back('请选择月份');
        }

        if (isset($_GET['channel']) and !empty ($_GET['channel'])) {
            $gets['channel'] = (int)$_GET['channel'];
            $price = number_format(self::$charge[$gets['channel']], 3);
        } else {
            \Swoole\JS::js_back('请选择渠道');
        }

        $start = date("Y-m-d H:i:s", strtotime($month));
        $end = date("Y-m-d H:i:s", strtotime("$month +1 month"));
        $gets['where'][] = 'addtime >= "' . $start . '"';
        $gets['where'][] = 'addtime <= "' . $end . '"';
        //\Swoole::$php->db("platform")->debug = 1;

        $gets['order'] = 'id desc';
        $gets['group'] = 'days';
        $gets['select'] = "DATE_FORMAT(addtime,'%Y-%m-%d') days,COUNT(id) as c";
        $data = table("sms_log", "platform")->gets($gets);
        $cost = 0;
        $count = 0;
        $line = "日期,条数,费用合计\n";
        foreach ($data as $k => $d) {
            if (!empty($d['c'])) {
                $data[$k]['cost'] = number_format($d['c'] * (self::$charge[$gets['channel']]), 3, '.', '');
                $cost += $data[$k]['cost'];
                $count += $data[$k]['c'];
            }
            $line .= "{$d['days']},{$d['c']},{$data[$k]['cost']}\n";
        }
        $line .= "\n";
        $line .= ",总计条数,总计费用\n";
        $line .= ",{$count},{$cost}\n";
        $line .= "备注: 渠道 " . self::$channel[$gets['channel']] . " 单价 {$price}元\n";
        $filename = self::$channel[$gets['channel']] . "-" . $month . ".csv";
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $line;
    }

    private function getSelect($now, $count)
    {
        $temp = date("Y-m", strtotime("$now"));
        $end = date("Y-m", strtotime("$now -{$count} year"));
        $time = array();
        while ($temp >= $end) {
            $time[$temp] = $temp;
            $temp = date("Y-m", strtotime("$temp -1 month"));

        }
        return $time;
    }
}