<?php
require_once __DIR__.'/config.php';
$gets = array();
$gets['order'] = 'id asc';
$modules = table('module')->gets($gets);
$prefix = 'YYPUSH';
foreach ($modules as $module)
{
    $params = array();
    if (  ($module['succ_hold'] > 0 or $module['wave_hold'] > 0)
        and (!empty($module['backup_uids']) or !empty($module['owner_uid'])) and  $module['alert_int'] > 0
    ) {
        $alert_ids = '';
        if (!empty($module['backup_uids'])) {
            $alert_ids = $module['backup_uids'];
        }
        if (!empty($module['owner_uid'])) {
            $alert_ids .= "," . $module['owner_uid'];
        }
        $params['module_id'] = $module['id'];
        $params['module_name'] = $module['name'];
        $gets = array();
        $gets['select'] = 'id,mobile,weixinid,username';
        $gets['where'][] = 'id in (' . $alert_ids . ')';
        $tmp = table('user', 'platform')->gets($gets);
        $user = array();
        $weixin = array();
        $alert = array();
        foreach ($tmp as $t) {
            if (!empty($t['mobile'])) {
                $user[$t['id']] = $t['mobile'];
                $alert[$t['id']]['mobile'] = $t['mobile'];
            }
            if (!empty($t['weixinid'])) {
                $weixin[$t['id']] = $t['username'];
                $alert[$t['id']]['weixinid'] = $t['username'];
            }
        }

        $params['module_id'] = $module['id'];
        $params['module_name'] = $module['name'];
        $params['enable_alert'] = $module['enable_alert'];
        $params['alert_uids'] = $alert_ids;
        $params['alert_mobiles'] = implode(',', $user);
        $params['alert_weixins'] = implode('|', $weixin);
        $params['alerts'] = json_encode($alert);
        $params['alert_int'] = $module['alert_int'];
        $params['succ_hold'] = $module['succ_hold'];
        $params['wave_hold'] = $module['wave_hold'];

        if (!empty($alert)) {

            $res = table('interface')->gets(array('module_id' => $module['id']));
            foreach ($res as $re) {
                \Swoole::$php->redis->sAdd($prefix, $re['id']);//添加接口集合
            }
            $key = $prefix . "::MODULE::" . $module['id'];
            \Swoole::$php->redis->hMset($key, $params);
            echo "init module {$module['id']} with {$key} {$params['alert_weixins']}\n";
        }

    }
}
