<?php
namespace App\Controller;

class App_host extends \App\LoginController
{
    function add_project()
    {
        $form['name'] = \Swoole\Form::input('name');
        $form['identifier'] = \Swoole\Form::input('identifier');
        $form['description'] = \Swoole\Form::text('description', '', array('cols' => 80, 'rows' => 10));

        $this->assign('form', $form);

        $this->display('app_host/edit_project.php');
    }

    function project_list()
    {
        $this->display();
    }

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
        $this->display();
    }

    function rule_list()
    {
        $this->display();
    }
}
