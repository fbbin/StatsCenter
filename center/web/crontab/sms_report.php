<?php
ini_set("memory_limit","1024M");
define('SWOOLE_SERVER', true);
require_once dirname(__DIR__).'/config.php';
require dirname(__DIR__).'/apps/include/mail.php';
$start_time = microtime(true);
$now = date("Y-m");
//$now = date("Y-m",strtotime("-4 month"));
$month_start = date("Y-m",strtotime("$now -1 month"));
$month_end = date("Y-m",strtotime("$now"));
echo ("[".date("Y-m-d H:i:s")."] sms_report start to report {$month_start} \n");
$start = date("Y-m-d H:i:s", strtotime($month_start));
$end = date("Y-m-d H:i:s", strtotime($month_end));
$gets['where'][] = 'addtime >= "'.$start.'"';
$gets['where'][] = 'addtime < "'.$end.'"';
//\Swoole::$php->db("platform")->debug = 1;
$path = "/data/config/platform/sms.conf";
$json = file_get_contents($path);
$channel_info = json_decode($json,1);
$channel = $channel_price = array();
foreach ($channel_info['all'] as $id => $c)
{
    $channel[$id] = $c['name'];
    $channel_price[$id] = $c['price'];
}
$gets['order'] = 'id desc';
$gets['group'] = 'channel';
$gets['select'] = "channel,COUNT(id) as c";
$data = table("sms_log","platform")->gets($gets);
//$price = number_format(0.040,3);
$all = array();
foreach ($data as $d)
{
    if (!isset($all['all'])) {
        $all['all']['count'] = $d['c'];
    } else {
        $all['all']['count'] += $d['c'];
    }
    if (!isset($all[$d['channel']])) {
        $all[$d['channel']]['count'] = $d['c'];
    } else {
        $all[$d['channel']]['count'] += $d['c'];
    }
}

foreach ($all as $k => $a)
{
    if ($k == 5) {
        $price = number_format(0.043,3);
    } else {
        $price = number_format(0.040,3);
    }
    $price = $channel_price[$k];
    if ($k != 'all') {
        $all[$k]['price'] = $price;
        $all[$k]['total'] = number_format($a['count']*$price,3);
        $all['all']['total'] += $a['count']*$price;
    }
}
$all['all']['total'] = number_format($all['all']['total'],3);
$all['time'] = "{$month_start}月份";
echo ("[".date("Y-m-d H:i:s")."] sms_report get data ".print_r($all,1)."\n");
$string = get_html($all);
$config_dir = "/data/config/platform/sms_recv.conf";
$users = file_get_contents($config_dir);
$users = json_decode($users,1);
$curl = new \Swoole\Client\CURL();
$url = "http://192.168.1.70:8080/mail/send";
$addr = implode(',',$users['recipients']);
$cc = implode(',',$users['cc_list']);
$data = array(
    'addr' => $addr,
    'cc' => $cc,
    'subject' => "{$all['time']} 短信报表",
    'content' => "$string",
);

$res = $curl->post($url,$data);
$end_time = microtime(1);
$take_time = $end_time-$start_time;
echo ("[".date("Y-m-d H:i:s")."] sms_report report end with res:".var_export($res,1)." \n");
echo ("[".date("Y-m-d H:i:s")."] 耗时: {$take_time} s\n");

function get_html($all)
{
    global $channel;
    $header = '<lang="en-us"><head>
                <head>
                    <meta>
                    <title>短信报表</title>
                    <meta name="description" content="">
                    <meta name="author" content="">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
                    <style>
                        .table {
                            width: 100%;
                            margin-bottom: 18px;
                            border-collapse: collapse;
                            border-spacing: 0;
                            border-color: grey;
                        }
                        thead tr {
                            background-color: #eee;
                            background-image: -webkit-gradient(linear,0 0,0 100%,from(#f2f2f2),to(#fafafa));
                            background-image: -webkit-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -moz-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -ms-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -o-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            font-size: 16px;
                        }
                        .table-bordered>thead>tr>th, .table-bordered>thead>tr>td {
                            border: 1px solid #ddd;
                        }
                        .table>thead>tr>th {
                            vertical-align: bottom;
                            border-bottom: 2px solid #ddd;
                        }
                        .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
                            padding: 8px;
                            line-height: 1.428571429;
                            vertical-align: top;
                            border-top: 1px solid #ddd;
                        }
                        tbody {
                            display: table-row-group;
                            vertical-align: middle;
                            border-color: inherit;
                        }
                        th {
                            text-align: left;
                        }
                        th {
                            font-weight: bold;
                        }
                        .table thead tr, .fc-border-separate thead tr {
                            background-color: #eee;
                            background-image: -webkit-gradient(linear,0 0,0 100%,from(#f2f2f2),to(#fafafa));
                            background-image: -webkit-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -moz-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -ms-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -o-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                        }
                        td, th {
                            display: table-cell;
                            vertical-align: inherit;
                        }
                        table tbody tr:last-child td {
                            border-bottom: 0;
                        }
                        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
                            border: 1px solid #ddd;
                        }
                    </style>
                </head>
                <body>
                <div style="overflow: hidden;">
                <table style="width: 100%;
                            margin-bottom: 18px;
                            border-collapse: collapse;
                            border-spacing: 0;
                            border-color: grey;
                            " class="table table-bordered dataTable">
                    <thead>
                        <tr style="background-color: #eee;
                            background-image: -webkit-gradient(linear,0 0,0 100%,from(#f2f2f2),to(#fafafa));
                            background-image: -webkit-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -moz-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -ms-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -o-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            font-size: 16px;">
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;text-align: left;font-weight: bold;">'.$all['time'].' 使用'.$all['all']['count'].'条  总计费用： '.$all['all']['total'].'元</th>
                        </tr>
                    </thead>
                </table>
                <table style="width: 100%;
                            margin-bottom: 18px;
                            border-collapse: collapse;
                            border-spacing: 0;
                            border-color: grey;
                            " class="table table-bordered dataTable">
                    <thead>
                        <tr style="background-color: #eee;
                            background-image: -webkit-gradient(linear,0 0,0 100%,from(#f2f2f2),to(#fafafa));
                            background-image: -webkit-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -moz-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -ms-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -o-linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            background-image: -linear-gradient(top,#f2f2f2 0,#fafafa 100%);
                            font-size: 16px;">
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">渠道</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">单价</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">数量</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">费用汇总</th>
                        </tr>
                    </thead>
                    <tbody style="display: table-row-group;
                            vertical-align: middle;
                            border-color: inherit;">
                ';
    $body = '';
    foreach ($all as $k => $v)
    {
        if (!array_key_exists($k,$channel)) {
            continue;
        }
        $body .= '<tr height="32" style="background-color:#DFFFDF" width="100%">
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$channel[$k].'</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['price'].'元</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['count'].'条</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['total'].'元</td>
                </tr>';
    }

            $footer =    '</tbody>
                </table>
            </div></body></html>';
    return $header.$body.$footer;
}