<?php
ini_set("memory_limit","1024M");
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
else
{
    Swoole::$php->config->setPath(dirname(__DIR__).'/apps/configs/');
}

require dirname(__DIR__).'/apps/include/mail.php';
$start = microtime(true);
echo ("[".date("Y-m-d H:i:s")."] start to report \n");
//$i_gets['select'] = 'id,name';
//$i_gets['order'] = 'id asc';
//$project_info = table("project")->getMap($i_gets,'name' );

$i_gets['select'] = 'id,name,module_id';
$i_gets['order'] = 'name asc';
$interface_tmp = table("interface")->gets($i_gets );

$mid2interface_id = array();
$interface_info = array();
foreach ($interface_tmp as $v)
{
    $interface_info[$v['id']] = $v['name'];
    $mid2interface_id[$v['module_id']][$v['id']] = $v['id'];
}

$u_gets['select']= "id,username";
$user = table("user")->getMap($u_gets,"username");


$m_gets['select'] = 'id,name,owner_uid,backup_uids';
$module_tmp = table("module")->gets($m_gets);
$module_info = array();
$mid2username = array();
foreach ($module_tmp as $v)
{
    $module_info[$v['id']] = $v['name'];
    if (!empty($v['owner_uid']))
    {
        $uids = explode(',',$v['owner_uid']);
        foreach ($uids as $uid)
        {
            $mid2username['addr'][$v['id']][] = $user[$uid]."@chelun.com";
        }
    }
    if (!empty($v['backup_uids']))
    {
        $cc_uids = explode(',',$v['backup_uids']);
        foreach ($cc_uids as $uid)
        {
            $mid2username['cc'][$v['id']][] = $user[$uid]."@chelun.com";
        }
    }
}

foreach ($interface_info as $interface_id => $name)
{
    $res = save_interface_stats($interface_id,$name,$module_info);
}

foreach ($mid2interface_id as $mid => $interface_ids)
{
    if (!empty($interface_ids))
    {
        $content = get_cache($interface_ids);

        if ($content !== false)
        {
            $html = get_html($content);
            $subject = "模块调用统计报表-".date("Y-m-d",time()-3600*24).":".$module_info[$mid];
            $user = $mid2username['addr'][$mid];
            $cc = $mid2username['cc'][$mid];
            if (!empty($user))
            {
                $m = new \Apps\Mail();
                $res = $m->mail($user,$subject,$html,$cc);
                //$res = $m->mail(array("shiguangqi@chelun.com"),$subject,$html);
                unset($m);
                if ($res)
                {
                    echo "send success $subject to ".json_encode($user)." cc to ".json_encode($cc)."\n";
                }
                else
                {
                    echo "send failed $subject to ".json_encode($user)." cc to ".json_encode($cc)."\n";
                }
            }
        }
    }
}

$files = scandir(__DIR__."/cache");
if (!empty($files))
{
    foreach ($files as $file)
    {
        if (!in_array($file,array('.','..','.keep')))
        {
            unlink(__DIR__."/cache/$file");
        }
    }
}

$end = microtime(true);
echo ("[".date("Y-m-d H:i:s")."] end report \n");
$elapsed = $end-$start;
echo ("[".date("Y-m-d H:i:s")."] elapsed time $elapsed s\n");

//----functions-----------------------
function get_cache($interface_ids)
{
    $files = scandir(__DIR__."/cache");
    $return = array();
    if (!empty($files))
    {
        foreach ($interface_ids as $interface_id)
        {
            $file = "interface_cache_$interface_id";
            if (in_array($file,$files))
            {
                $_interface = unserialize(file_get_contents(__DIR__."/cache/$file"));
                if (!empty($_interface))
                {
                    $return[$_interface['interface_id']] = $_interface;
                }
            }
        }
    }

    if (!empty($return))
        return $return;
    else
        return false;
}

function save_interface_stats($interface_id,$name,$module_info)
{
    $table = "stats_". date('Ymd',time()-3600*24);
    //$table = "stats_20150818";
    $gets['order'] = 'time_key asc';
    $gets['interface_id'] = $interface_id;
    $res = table($table)->gets($gets);
    if (!empty($res))
    {
        $caculate = array();
        foreach ($res as $v)
        {
            //基础接口信息
            if (!isset($caculate['interface_id']))
            {
                $caculate['interface_id'] = $v['interface_id'];
            }
            //基础模块信息
            if (!isset($caculate['module_id']))
            {
                $caculate['module_id'] = $v['module_id'];
            }
            //总数
            if (!isset($caculate['total_count']))
            {
                $caculate['total_count'] = $v['total_count'];
            }
            else
            {
                $caculate['total_count'] += $v['total_count'];
            }
            //失败汇总
            if (!isset($caculate['fail_count']))
            {
                $caculate['fail_count'] = $v['fail_count'];
            }
            else
            {
                $caculate['fail_count'] += $v['fail_count'];
            }
            //总时间汇总
            if (!isset($caculate['total_time']))
            {
                $caculate['total_time'] = $v['total_time'];
            }
            else
            {
                $caculate['total_time'] += $v['total_time'];
            }
            //总失败时间汇总 total_fail_time
            if (!isset($caculate['total_fail_time']))
            {
                $caculate['total_fail_time'] = $v['total_fail_time'];
            }
            else
            {
                $caculate['total_fail_time'] += $v['total_fail_time'];
            }

            //获取最大时间
            if (!isset($caculate['max_time']))
            {
                $caculate['max_time'] = $v['max_time'];
            }
            elseif($caculate['max_time'] < $v['max_time'])
            {
                $caculate['max_time'] = $v['max_time'];
            }
            //获取最小时间
            if (!isset($caculate['min_time']))
            {
                $caculate['min_time'] = $v['min_time'];
            }
            elseif($caculate['min_time'] > $v['min_time'])
            {
                $caculate['min_time'] = $v['min_time'];
            }
        }

        //平均响应时间
        if ($caculate['total_count'] != 0)
        {
            $caculate['avg_time'] = number_format($caculate['total_time'] / $caculate['total_count'],2);
            $caculate['succ_rate'] = number_format(($caculate['total_count']-$caculate['fail_count']) /
                    $caculate['total_count'],2)*100;
        }
        else
        {
            $caculate['avg_time'] = 0;
            $caculate['succ_rate'] = 0;
        }
        //平均失败响应时间
        if ($caculate['fail_count'] != 0)
        {
            $caculate['avg_fail_time'] = number_format($caculate['total_fail_time'] / $caculate['fail_count'],2);
        }
        else
        {
            $caculate['avg_fail_time'] = 0;
        }
        $caculate['succ_count'] = $caculate['total_count'] - $caculate['fail_count'];

        \Swoole\Filter::safe($name);
        $caculate['interface_name'] = $name;
        $module_name = isset($module_info[$caculate['module_id']])?$module_info[$caculate['module_id']]:'';
        \Swoole\Filter::safe($module_name);
        $caculate['module_name'] = $module_name;
        return file_put_contents(__DIR__."/cache/interface_cache_{$interface_id}",serialize($caculate));
    }
    else
    {
        return false;
    }
}

function get_html($content)
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
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;text-align: left;font-weight: bold;">模块名称</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">接口名称</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">调用次数</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">成功次数</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">失败次数</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">成功率</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">最大响应时间</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">最小响应时间</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">平均响应时间</th>
                            <th style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;border-bottom: 2px solid #ddd;">失败响应时间</th>
                        </tr>
                    </thead>
                    <tbody style="display: table-row-group;
                            vertical-align: middle;
                            border-color: inherit;">
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
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['module_name'].'</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['interface_name'].'</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['total_count'].'</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['succ_count'].'</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['fail_count'].'</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['succ_rate'].'%</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['max_time'].'ms</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['min_time'].'ms</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['avg_time'].'ms</td>
                    <td style="border: 1px solid #ddd;display: table-cell;vertical-align: inherit;">'.$v['avg_fail_time'].'ms</td>
                </tr>';
    }

            $footer =    '</tbody>
                </table>
            </div></body></html>';
    return $header.$body.$footer;
}