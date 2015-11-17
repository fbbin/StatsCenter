<?php
namespace App\Controller;
use Swoole;
use Sdk;

class Page extends Swoole\Controller
{
    const APP_ID = 1;
    const APP_KEY = 'hell';

    function index()
    {
        $this->login();
    }

    function logout()
    {
        $this->session->start();
        $_SESSION = array();
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

        if (!empty($_SESSION['isLogin']))
        {
            home:
            $this->swoole->http->redirect($this->swoole->config['user']['home_url']);
            return;
        }
        Swoole\Loader::addNameSpace('Sdk', PUBLIC_PATH.'/sdk/Sdk/');
        $sdk = new Sdk\Employee(self::APP_ID, self::APP_KEY);
        //跳转到登录平台
        if (empty($_GET['token']))
        {
            $login_url = $sdk->getLoginUrl(WEBROOT . '/page/login/' . $refer);
            $this->http->redirect($login_url);
        }
        else
        {
            $userinfo = $sdk->getUserInfo($_GET['token']);
            $_SESSION['userinfo'] = $userinfo;
            $_SESSION['user_id'] = $userinfo['id'];
            $_SESSION['isLogin'] = true;
            if (!empty($_GET['refer']))
            {
                $this->swoole->http->redirect(WEBROOT . base64_decode($_GET['refer']));
            }
            else
            {
                goto home;
            }
        }
    }
}