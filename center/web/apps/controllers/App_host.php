<?php
namespace App\Controller;

class App_host extends \App\LoginController
{
    function add_host()
    {
        $form['name'] = \Swoole\Form::input('name');
        $form['identifier'] = \Swoole\Form::input('identifier');
        $form['host'] = \Swoole\Form::input('host');

        $this->assign('form', $form);

        $this->display('app_host/edit_host.php');
    }

    function host_list()
    {
        $this->display();
    }

    function add_rule()
    {
        $form['uid'] = \Swoole\Form::input('uid');
        $form['openUDID'] = \Swoole\Form::input('openUDID');
        $form['identifier'] = \Swoole\Form::input('identifier');

        $this->assign('form', $form);

        $this->display('app_host/edit_rule.php');
    }

    function rule_list()
    {
        $this->display();
    }
}
