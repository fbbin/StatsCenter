<?php
namespace App\Controller;
use Swoole;

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

        $user_info = table("user")->get($id)->get();
        if (!empty($_POST))
        {
            //编辑
            $id = $_POST['id'];
            unset($_POST['id']);
            if (!empty($_POST['password']))
            {
                $_POST['password'] = Swoole\Auth::mkpasswd($user_info['username'], trim($_POST['password']));
            }
            $res = table("user")->set($id, $_POST);
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
            $form['realname'] = \Swoole\Form::input('realname', $user_info['realname'], array('type' => 'tel'));
        }
        $this->assign('form', $form);
        $this->display();
    }

    function add()
    {
        //不是超级用户不能查看修改用户
        if ($this->userinfo['usertype'] != 0)
        {
            return "access deny";
        }
        //\Swoole::$php->db->debug = true;
        if (empty($_GET) and empty($_POST))
        {
            $tmp = table('project')->gets(array("order"=>"id desc"));
            $project = array();
            foreach ($tmp as $t)
            {
                $project[$t['id']] = $t['name'];
            }
            $form['project_id'] = \Swoole\Form::muti_select('project_id[]',$project,array(),null,array('class'=>'select2 select2-offscreen','multiple'=>"1",'style'=>"width:100%" ),false);
            $form['uid'] = \Swoole\Form::input('uid');
            $form['mobile'] = \Swoole\Form::input('mobile');
            $form['realname'] = \Swoole\Form::input('realname');
            $form['username'] = \Swoole\Form::input('username');
            $form['usertype'] = \Swoole\Form::select('usertype', $this->config['usertype'], null, null, array('class' => 'select2'));
            $this->assign('form', $form);
            $this->display();
        }
        elseif (!empty($_GET['id']) and empty($_POST))
        {
            $id = (int)$_GET['id'];
            $user = table('user')->get($id)->get();

            $tmp = table('project')->gets(array("order"=>"id desc"));
            $project = array();
            foreach ($tmp as $t)
            {
                $project[$t['id']] = $t['name'];
            }
            $form['project_id'] = \Swoole\Form::muti_select('project_id[]',$project,explode(',',$user['project_id']),null,array('class'=>'select2 select2-offscreen','multiple'=>"1",'style'=>"width:100%" ),false);
            $form['uid'] = \Swoole\Form::input('uid',$user['uid']);
            $form['mobile'] = \Swoole\Form::input('mobile',$user['mobile']);
            $form['realname'] = \Swoole\Form::input('realname',$user['realname']);
            $form['username'] = \Swoole\Form::input('username',$user['username']);
            $form['id'] = \Swoole\Form::hidden('id',$user['id']);
            $this->assign('form', $form);
            $this->display();
        }
        elseif (!empty($_POST['id']))
        {
            $id = (int)$_POST['id'];
            $inserts['realname'] = $_POST['realname'];
            $inserts['username'] = $_POST['username'];
            $inserts['uid'] = (int)$_POST['uid'];
            $inserts['project_id'] = implode(',',$_POST['project_id']);
            $inserts['mobile'] = $_POST['mobile'];

            $res = table("user")->set($id,$inserts);
            if ($res)
            {
                \Swoole\JS::js_goto("修改成功",'/user/ulist/');
            }
            else
            {
                \Swoole\JS::js_goto("修改失败",'/user/ulist/');
            }
        }
        else
        {
            $inserts['realname'] = $_POST['realname'];
            $inserts['username'] = trim($_POST['username']);
            $inserts['uid'] = (int)$_POST['uid'];
            $inserts['project_id'] = implode(',',$_POST['project_id']);
            $inserts['mobile'] = $_POST['mobile'];
            //默认密码
            $inserts['password'] = Swoole\Auth::mkpasswd($inserts['username'], '123456');

            $res = table("user")->put($inserts);
            if ($res)
            {
                \Swoole\JS::js_goto("添加成功",'/user/ulist//');
            }
            else
            {
                \Swoole\JS::js_goto("添加失败",'/user/ulist/');
            }
        }
    }

    function passwd()
    {
        if (!empty($_POST['old_password']))
        {
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
                    $msg['message'] = '密码修改成功';
                    $msg['code'] = 0;
                }
                else
                {
                    $msg['message'] = $this->user->errMessage;
                    $msg['code'] = $this->user->errCode;
                }
            }
            $this->assign('msg', $msg);
        }
        $this->display();
    }

    function ulist()
    {
        //不是超级用户不能查看修改用户
        if ($this->userinfo['usertype'] != 0)
        {
            return "access deny";
        }
        $gets = array();
        if (!empty($_POST['uid']))
        {
            $uid = intval(trim($_POST['uid']));
            $gets['where'][] = "uid={$uid}";
            $_GET['uid'] = $uid;
        }
        if (!empty($_POST['username']))
        {
            $name = trim($_POST['username']);
            $gets['where'][] = "username like '%{$name}%'";
            $_GET['username'] = $name;
        }
        if (!empty($_POST['realname']))
        {
            $name = trim($_POST['realname']);
            $gets['where'][] = "realname like '%{$name}%'";
            $_GET['realname'] = $name;
        }

        $gets["order"] = 'addtime desc';
        $gets['page'] = !empty($_GET['page'])?$_GET['page']:1;
        $gets['pagesize'] = 20;
        $data = table("user")->gets($gets,$pager);
        $this->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
        $this->assign('data', $data);
        $this->display();
    }

    function tip()
    {
        $this->display();
    }
}