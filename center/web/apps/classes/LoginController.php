<?php
namespace App;

class LoginController extends \Swoole\Controller
{
    protected $uid;
    protected $userinfo;

    function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $swoole->session->start();
        if (!$this->user->isLogin())
        {
            if (!empty($_SERVER['QUERY_STRING']))
            {
                $this->swoole->http->redirect($this->swoole->config['user']['login_url']."?refer=".base64_encode($_SERVER['REQUEST_URI']));
            }
            else
            {
                $this->swoole->http->redirect($this->swoole->config['user']['login_url']);
            }
            $this->swoole->http->finish();
        }
        else
        {
            $this->uid = $this->user->getUid();
            $this->userinfo = $_SESSION['userinfo'];
        }
    }

    function isAllow($optype, $id)
    {
        if ($_SESSION['userinfo']['usertype'] == 0)
        {
            return true;
        }
    }
}