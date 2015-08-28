<?php
error_reporting(0);
define('DEBUG', 'off');
define('WEBPATH', __DIR__);
require dirname(__DIR__).'/../../framework/libs/lib_config.php';
require dirname(__DIR__).'/apps/include/functions.php';
$env = get_cfg_var('env.name');

if ($env == 'local')
{
    Swoole::$php->config->setPath(dirname(__DIR__).'/apps/configs/local/');
}
elseif ($env == 'dev')
{
    Swoole::$php->config->setPath(dirname(__DIR__).'/apps/configs/dev/');
}


require dirname(__DIR__).'/apps/include/mail.php';
$m = new \Apps\Mail();
$content = get_content();
$html = render($content);

$subject = "模块调用统计报表-".date("Y-m-d");
$addr = array(
    'shiguangqi@chelun.com',
    'hantianfeng@chelun.com',
);
$cc = array(
    'shiguangqi@chelun.com'
);
$res = $m->mail('shiguangqi@chelun.com',$subject,$html,$cc);


function get_content()
{
    $content = '';
    $table = "stats_". date('Ymd');
    $gets['order'] = 'interface_id asc,time_key asc';
    $res = table($table)->gets($gets);
    $all_interface = array();
    foreach ($res as $v)
    {
        $all_interface[$v['interface_id']][] = $v;
    }
    $caculate = array();
    foreach ($all_interface as $interface_id => $faces)
    {
        foreach ($faces as $v)
        {
            //基础接口信息
            if (!isset($caculate[$interface_id]['interface_id']))
            {
                $caculate[$interface_id]['interface_id'] = $v['interface_id'];
            }
            //基础模块信息
            if (!isset($caculate[$interface_id]['module_id']))
            {
                $caculate[$interface_id]['module_id'] = $v['module_id'];
            }
            //总数
            if (!isset($caculate[$interface_id]['total_count']))
            {
                $caculate[$interface_id]['total_count'] = $v['total_count'];
            }
            else
            {
                $caculate[$interface_id]['total_count'] += $v['total_count'];
            }
            //失败汇总
            if (!isset($caculate[$interface_id]['fail_count']))
            {
                $caculate[$interface_id]['fail_count'] = $v['fail_count'];
            }
            else
            {
                $caculate[$interface_id]['fail_count'] += $v['fail_count'];
            }
            //总时间汇总
            if (!isset($caculate[$interface_id]['total_time']))
            {
                $caculate[$interface_id]['total_time'] = $v['total_time'];
            }
            else
            {
                $caculate[$interface_id]['total_time'] += $v['total_time'];
            }
            //总失败时间汇总 total_fail_time
            if (!isset($caculate[$interface_id]['total_fail_time']))
            {
                $caculate[$interface_id]['total_fail_time'] = $v['total_fail_time'];
            }
            else
            {
                $caculate[$interface_id]['total_fail_time'] += $v['total_fail_time'];
            }
            //获取最大时间
            if (!isset($caculate[$interface_id]['max_time']))
            {
                $caculate[$interface_id]['max_time'] = $v['max_time'];
            }
            elseif($caculate[$interface_id]['max_time'] < $v['max_time'])
            {
                $caculate[$interface_id]['max_time'] = $v['max_time'];
            }
            //获取最小时间
            if (!isset($caculate[$interface_id]['min_time']))
            {
                $caculate[$interface_id]['min_time'] = $v['min_time'];
            }
            elseif($caculate[$interface_id]['min_time'] > $v['min_time'])
            {
                $caculate[$interface_id]['min_time'] = $v['min_time'];
            }
        }
    }
    $i_gets['select'] = 'id,name';
    $interface_info = table("interface")->getMap($i_gets,'name');
    $m_gets['select'] = 'id,name';
    $module_info = table("module")->getMap($i_gets,'name');

    //平均时间计算
    foreach ($caculate as $k => $count)
    {
        //平均响应时间
        if ($count['total_count'] != 0)
        {
            $res = $count['total_time'] / $count['total_count'];
            $caculate[$k]['avg_time'] = number_format($res,2);
            $caculate[$k]['succ_rate'] = number_format(($count['total_count']-$count['fail_count']) /
                    $count['total_count'],2)*100;
        }
        else
        {
            $caculate[$k]['avg_time'] = 0;
            $caculate[$k]['succ_rate'] = 0;
        }
        //平均失败响应时间
        if ($count['fail_count'] != 0)
        {
            $res = $count['total_fail_time'] / $count['fail_count'];
            $caculate[$k]['avg_fail_time'] = number_format($res,2);
        }
        else
        {
            $caculate[$k]['avg_fail_time'] = 0;
        }
        $caculate[$k]['succ_count'] = $count['total_count'] - $count['fail_count'];

        $interface_name = $interface_info[$count['interface_id']];
        \Swoole\Filter::safe($interface_name);
        $caculate[$k]['interface_name'] = $interface_name;
        $module_name = $module_info[$count['module_id']];
        \Swoole\Filter::safe($module_name);
        $caculate[$k]['module_name'] = $module_name;
    }
    return $caculate;
}

function render($content)
{
    $header = '<lang="en-us"><head>
                <head>
                    <meta>
                    <title>模调统计系统</title>
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
                <table class="table table-bordered dataTable">
                    <thead>
                        <tr>
                            <th>模块名称</th>
                            <th>接口名称</th>
                            <th>调用次数</th>
                            <th>成功次数</th>
                            <th>失败次数</th>
                            <th>成功率</th>
                            <th>最大响应时间</th>
                            <th>最小响应时间</th>
                            <th>平均响应时间</th>
                            <th>失败响应时间</th>
                        </tr>
                    </thead>
                    <tbody>
                ';
    $body = '';
    foreach ($content as $v)
    {
        if ($v['succ_rate'] > 90)
        {
            $bg_color = "#DFFFDF";
        } else {
            $bg_color = "#FFDFDF";
        }
        $body .= '<tr height="32" style="background-color:'.$bg_color.'" width="100%">
                    <td>'.$v['module_name'].'</td>
                    <td>'.$v['interface_name'].'</td>
                    <td>'.$v['total_count'].'</td>
                    <td>'.$v['succ_count'].'</td>
                    <td>'.$v['fail_count'].'</td>
                    <td>'.$v['succ_rate'].'%</td>
                    <td>'.$v['max_time'].'ms</td>
                    <td>'.$v['min_time'].'ms</td>
                    <td>'.$v['avg_time'].'ms</td>
                    <td>'.$v['avg_fail_time'].'ms</td>
                </tr>';
    }

            $footer =    '</tbody>
                </table>
            </div></body></html>';
    return $header.$body.$footer;
}