<?php
namespace App;

class LoginController extends \Swoole\Controller
{
    protected $uid;
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
        }
    }
}