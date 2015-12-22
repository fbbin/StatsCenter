<?php
namespace App\Controller;
use Swoole;
use App;

class User extends \App\LoginController
{
    function edit()
    {
        //\Swoole\Error::dbd();
        $id = $this->uid;
        if (empty($id))
        {
            \Swoole\JS::js_back("操作不合法");
        }

        $user_info = table("user", 'platform')->get($id)->get();
        if (!empty($_POST))
        {
            //编辑
            $id = $_POST['id'];
            unset($_POST['id']);

            $user['username'] = $user_info['username'];
            $user['realname'] = trim($_POST['realname']);
            $user['weixinid'] = trim($_POST['weixinid']);
            $user['mobile'] = trim($_POST['mobile']);
            $this->addWeiXin($user);
            $res = table("user", 'platform')->set($id, $_POST);

            //同步到内网平台
            $this->syncIntranet($user['username'], [
                'fullname' => $user['realname'],
                'phone' => $user['mobile'],
            ]);

            if ($res)
            {
                \Swoole\JS::js_goto("更新成功",'/user/edit/');
            }
            else
            {
                \Swoole\JS::js_goto("更新失败",'/user/edit/');
            }
        }
        else
        {
            //展示
            $form['id'] = \Swoole\Form::hidden('id', $user_info['id']);
            $form['mobile'] = \Swoole\Form::input('mobile', $user_info['mobile']);
            $form['weixinid'] = \Swoole\Form::input('weixinid', $user_info['weixinid']);
            $form['realname'] = \Swoole\Form::input('realname', $user_info['realname'], array('type' => 'tel'));
        }
        $this->assign('form', $form);
        $this->display();
    }

    function passwd()
    {
        if (empty($_POST['old_password']))
        {
            $this->display();
            return;
        }
        if (empty($_POST['new_password']) or empty($_POST['new_password2']))
        {
            $msg['message'] = '新密码不能为空';
            $msg['code'] = 200;
        }
        elseif ($_POST['new_password'] == $_POST['old_password'])
        {
            $msg['message'] = '新密码与旧密码不能相同';
            $msg['code'] = 201;
        }
        elseif ($_POST['new_password'] != $_POST['new_password2'])
        {
            $msg['message'] = '密码前后输入不一致';
            $msg['code'] = 201;
        }
        else
        {
            $ret = $this->user->changePassword($this->uid, $_POST['old_password'], $_POST['new_password']);
            if ($ret)
            {
                //是否有GIT账户
                if (!empty($this->userinfo['git_password']))
                {
                    $git_password = $this->getGitPassword($_POST['new_password']);
                    $update = ['git_password' => $git_password, ];
                    table('user', 'platform')->set($this->uid, $update);
                    //同步到内网平台
                    if (!$this->syncIntranet($this->userinfo['username'], $update))
                    {
                        goto fail;
                    }
                }
                App\CrowdUser::setPassword($this->userinfo['username'], $_POST['new_password']);
                $msg['message'] = '密码修改成功';
                $msg['code'] = 0;
            }
            else
            {
                fail:
                $msg['message'] = $this->user->errMessage;
                $msg['code'] = $this->user->errCode;
            }
        }
        $this->assign('msg', $msg);
        $this->display();
    }

    function tip()
    {
        $this->display();
    }
}
