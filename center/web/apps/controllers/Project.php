<?php
namespace App\Controller;
use Swoole;

class Project extends \App\LoginController
{
    function delete()
    {
        if (empty($_GET['id']))
        {
            return "缺少参数";
        }
        $id = intval($_GET['id']);
        if (!$this->isAllow(__METHOD__, $id))
        {
            return '没有权限删除';
        }
        if (table('project')->del($id))
        {
            return Swoole\JS::js_back("删除成功");
        }
        else
        {
            return Swoole\JS::js_back("删除失败");
        }
    }

    function edit()
    {
        if (!empty($_POST['name']))
        {
            $inserts['name'] = $_POST['name'];
            $inserts['intro'] = $_POST['intro'];
            $msg['code'] = 0;

            if (empty($_POST['id']))
            {
                $res = table("project")->put($inserts);
                $msg['message'] = $res ? "添加成功，ID: " . $res : "添加失败";
            }
            else
            {
                $res = table("project")->set(intval($_POST['id']),$inserts);
                $msg['message'] = $res ? "修改成功" : "修改失败";
            }
            $this->assign('msg', $msg);
        }

        if (empty($_GET))
        {
            $form['name'] = \Swoole\Form::input('name');
            $form['intro'] = \Swoole\Form::input('intro');
            $this->assign('form', $form);
        }
        else
        {
            $id = (int)$_GET['id'];
            $res = table("project")->get($id)->get();
            $form['name'] = \Swoole\Form::input('name', $res['name']);
            $form['intro'] = \Swoole\Form::input('intro', $res['intro']);
            $form['id'] = \Swoole\Form::hidden('id', $res['id']);
        }
        $this->assign('form', $form);
        $this->display();
    }

    function plist()
    {
        $gets = array();
        if (!empty($_POST['id']))
        {
            $id = intval(trim($_POST['id']));
            $gets['where'][] = "id={$id}";
            $_GET['id'] = $id;
        }
        if (!empty($_POST['name']))
        {
            $name = trim($_POST['name']);
            $gets['where'][] = "name like '%{$name}%'";
            $_GET['name'] = $name;
        }
        $gets["order"] = 'add_time desc';
        $gets['page'] = !empty($_GET['page'])?$_GET['page']:1;
        $gets['pagesize'] = 20;
        $data = table("project")->gets($gets,$pager);
        $this->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
        $this->assign('data', $data);
        $this->display();
    }
}