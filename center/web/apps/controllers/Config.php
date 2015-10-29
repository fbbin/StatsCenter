<?php
namespace App\Controller;

use Swoole;

require_once '/data/www/public/sdk/StatsCenter.php';
require_once '/data/www/public/sdk/CloudConfig.php';

class Config extends \App\LoginController
{
    function index()
    {
        \CloudConfig::$AOPNET_SVR_IP = '127.0.0.1';
        $categorys = \CloudConfig::getFromCloud('config:category', 'system');
        $category = empty($_GET['category']) ? array_keys($categorys)[0] : trim($_GET['category']);
        try
        {
            $list = \CloudConfig::getFromCloud('config:list:'.$category, 'system');
        }
        catch(\ConfigNotFound $e)
        {
            $list = array();
        }

        $this->assign('list', $list);
        $this->assign('category', $category);
        $this->assign('categorys', $categorys);
        $this->display();
    }

    function entity()
    {
        $this->assign('json', '{}');
        $this->display();
    }
}