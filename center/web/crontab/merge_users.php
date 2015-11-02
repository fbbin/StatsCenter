<?php
/**
 * 合并Code平台和模调平台的用户信息
 */
ini_set("memory_limit","1024M");
define('SWOOLE_SERVER', true);
require_once dirname(__DIR__).'/config.php';

$userTable = table('user', 'platform');
$users = table('adm_user', 'platform')->gets(['order' => 'aid desc']);
foreach($users as $u)
{
    //是否存在
    $u2 = $userTable->get($u['username'], 'username');
    if ($u2->exist())
    {
        $u2->git_password = $u['git_password'];
        $u2->svn_password = $u['password'];
        $u2->md5_password = $u['md5_password'];
        //Code平台有，模调系统没有
        if (!empty($u['password']) and empty($u2->mobile))
        {
            $u2->mobile = $u['phone'];
        }
        $u2->gid = $u['gid'];
        $u2->code_type = $u['type'];
        $u2->save();
        echo "更新 {$u['username']} 账户信息 成功\n";
    }
    else
    {
        $put['git_password'] = $u['git_password'];
        $put['svn_password'] = $u['password'];
        $put['md5_password'] = $u['md5_password'];
        $put['gid'] = $u['gid'];
        $put['mobile'] = $u['phone'];
        $put['username'] = $u['username'];
        $put['realname'] = $u['fullname'];
        $put['addtime'] = date('Y-m-d H:i:s', $u['ctime']);
        $put['code_type'] = $u['type'];
        $put['usertype'] = 1;
        $userTable->put($put);
        echo "插入 {$u['username']} 账户信息 成功\n";
    }
}
