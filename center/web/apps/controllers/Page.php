<?php
namespace App\Controller;
use Swoole;

class Page extends Swoole\Controller
{
    function index()
    {
        $this->swoole->session->start();
        if (!empty($_SESSION['isLogin']))
        {
            $this->swoole->http->redirect($this->swoole->config['user']['home_url']);
        }
        else
        {
            if (!empty($_GET['refer']))
            {
                $refer = '?refer='.$_GET['refer'];
            }
            else
            {
                $refer = '';
            }
            $this->assign('refer', $refer);
            $this->display();
        }
    }

    function logout()
    {
        $this->user->logout();
        $this->swoole->http->redirect($this->swoole->config['user']['login_url']);
    }

    function login()
    {
        $this->session->start();
        if (!empty($_GET['refer']))
        {
            $refer = '?refer='.$_GET['refer'];
        }
        else
        {
            $refer = '';
        }

        //$this->db->debug = true;
        if ($this->user->isLogin())
        {
            home:
            $this->swoole->http->redirect($this->swoole->config['user']['home_url'] . $refer);
        }
        elseif ($this->user->login($_POST['username'], $_POST['password']))
        {
            $_SESSION['userinfo'] = $this->user->getUserInfo();
            $_SESSION['realname'] = urldecode($_COOKIE['sysop_privilege_nick_name']);
            goto home;
        }
        else
        {
            Swoole\JS::js_back("用户名或密码错误");
        }
    }

    function collect_user()
    {
        $uid = $_COOKIE['yyuid'];
        if (!table('user', 'platform')->exists(array('uid' => $uid)))
        {
            $puts['uid'] = $uid;
            $puts['username'] = $_COOKIE['username'];
            if (isset($_COOKIE['sysop_privilege_nick_name']) and !empty($_COOKIE['sysop_privilege_nick_name']))
                $puts['realname'] = urldecode($_COOKIE['sysop_privilege_nick_name']);
            table('user', 'platform')->put($puts);
        }
    }
}