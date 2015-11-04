<?php
namespace App\Controller;
use Swoole;

/**
 * 系统管理，只有超级管理员有权限
 * @package App\Controller
 */
class Setting extends \App\LoginController
{
    public $alert_types = array(
        1 => "谈窗",
        2 => '短信'
    );

    static $roles = array(
        'app' => '客户端控制',
        'stats' => '模调统计',
        'url' => '短链接系统',
    );

    function add_interface()
    {
        $gets['select'] = 'id,username,realname,mobile';
        $tmp = table('user', 'platform')->gets($gets);
        $user = array();
        $mobile = array();
        foreach ($tmp as $t)
        {
            $name = !empty($t['realname']) ? $t['realname'] : '';
            $user[$t['id']] = "{$name} [{$t['username']}]";
            if (!empty($t['mobile']))
            {
                $mobile[$t['mobile']] = $name . $t['mobile'];
                $user[$t['id']] = "{$name} [{$t['username']}]-{$t['mobile']}";
            }
        }
        //\Swoole\Error::dbd();
        //新增操作
        if (empty($_GET['id']) and empty($_POST))
        {
            $params['title'] = '新增接口';
            $params['data'] = array();
            $m_params['select'] = 'id,name';
            $module = table('module')->getMap($m_params, 'name');
            $form['module_id'] = \Swoole\Form::select('module_id', $module, '', null, array('class' => 'select2 select2-offscreen', 'style' => "width:100%"));
            $form['alert_uids'] = \Swoole\Form::muti_select('alert_uids[]', $user, array(), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
            $form['owner_uid'] = \Swoole\Form::select('owner_uid', $user, '', null, array('class' => 'select2 select2-offscreen', 'style' => "width:100%"));
            $form['backup_uids'] = \Swoole\Form::muti_select('backup_uids[]', $user, array(), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
            $this->assign('user', $user);
            $this->assign('data', $params);
        }
        elseif (!empty($_GET['id']) and empty($_POST))
        {
            //修改页面
            $id = (int)$_GET['id'];
            $data = table('interface')->get($id)->get();
            $m_params['select'] = 'id,name';
            $module = table('module')->getMap($m_params, 'name');
            $form['module_id'] = \Swoole\Form::select('module_id', $module, $data['module_id'], null, array('class' => 'select2 select2-offscreen', 'style' => "width:100%"));
            $form['alert_uids'] = \Swoole\Form::muti_select('alert_uids[]', $user, !empty($data['alert_uids']) ? explode(',', $data['alert_uids']) : array(), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
            $form['owner_uid'] = \Swoole\Form::select('owner_uid', $user, $data['owner_uid'], null, array('class' => 'select2 select2-offscreen', 'style' => "width:100%"), false);
            $form['backup_uids'] = \Swoole\Form::muti_select('backup_uids[]', $user, !empty($data['backup_uids']) ? explode(',', $data['backup_uids']) : array(), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
            $this->assign('form', $form);
            $params['title'] = '修改接口';
            if (empty($data))
            {
                $msg['code'] = 400;
                $msg['message'] = "错误操作";
                $this->assign('msg', $msg);
            } else {
                if (!empty($data['alert_types']))
                    $data['alert_types'] = explode('|',$data['alert_types']);
                $params['data'] = $data;
            }
            $this->assign('data', $params);
        }
        elseif (!empty($_POST))
        {
            //入库操作
            if (empty($_POST['id'])) //新增
            {
                $params['title'] = '新增接口';

                $in['name'] = trim($_POST['name']);
                $in['module_id'] = trim($_POST['module_id']);
                $in['alias'] = trim($_POST['alias']);
                $in['succ_hold'] = trim($_POST['succ_hold']);
                $in['wave_hold'] = trim($_POST['wave_hold']);
                $in['alert_int'] = trim($_POST['alert_int']);

                if (!empty($_POST['enable_alert']))
                {
                    $in['enable_alert'] = trim($_POST['enable_alert']);
                }

//                $alert_uids = '';
//                if (!empty($_POST['alert_uids']))
//                {
//                    $alert_uids = implode(',', $_POST['alert_uids']);
//                }
//                $in['alert_uids'] = $alert_uids;

                $alert_types = '';
                if (!empty($_POST['$alert_types']))
                {
                    $alert_types = implode('|', $_POST['$alert_types']);
                }
                $in['alert_types'] = $alert_types;

                $in['owner_uid'] = trim($_POST['owner_uid']);
                if (empty($in['owner_uid']))
                {
                    $in['owner_uid'] = $this->uid;
                }

                $backup_uids = '';
                if (!empty($_POST['backup_uids']))
                {
                    $backup_uids = implode(',', $_POST['backup_uids']);
                }
                $in['backup_uids'] = $backup_uids;
                $in['intro'] = trim($_POST['intro']);
                $in['owner_uid'] = $this->uid;
                $c = table('interface')->count(array('name' => $in['name'], 'module_id' => $in['module_id']));
                if ($c > 0)
                {
                    \Swoole\JS::js_goto("操作失败,该模块下已经存在{$in['name']}接口", '/setting/add_interface/');
                }
                else
                {
                    $insert_id = table('interface')->put($in);
                    if ($insert_id)
                    {
                        //更新redis 通知报警server
                        $this->_save_interface($insert_id, $in);
                        //
                        $params['data'] = $in;
                        $msg['code'] = 0;
                        $msg['message'] = "操作成功";
                        $this->assign('msg', $msg);
                    }
                    else
                    {
                        $msg['code'] = $this->db->errno;
                        $msg['message'] = "操作成功";
                        $this->assign('msg', $msg);
                    }
                }
                $this->assign('data', $params);
            }
            else
            { //修改
                $params['title'] = '修改接口';
                $id = (int)$_POST['id'];
                $in['name'] = trim($_POST['name']);
                $in['module_id'] = trim($_POST['module_id']);
                $in['alias'] = trim($_POST['alias']);
                $in['succ_hold'] = trim($_POST['succ_hold']);
                $in['wave_hold'] = trim($_POST['wave_hold']);
                $in['alert_int'] = trim($_POST['alert_int']);
                $in['enable_alert'] = trim($_POST['enable_alert']);

//                $alert_uids = '';
//                if (!empty($_POST['alert_uids']))
//                {
//                    $alert_uids = implode(',', $_POST['alert_uids']);
//                }
//                $in['alert_uids'] = $alert_uids;

                $alert_types = '';
                if (!empty($_POST['alert_types']))
                {
                    $alert_types = implode('|', $_POST['alert_types']);
                }
                $in['alert_types'] = $alert_types;

                $in['owner_uid'] = trim($_POST['owner_uid']);

                $backup_uids = '';
                if (!empty($_POST['backup_uids']))
                {
                    $backup_uids = implode(',', $_POST['backup_uids']);
                }
                $in['backup_uids'] = $backup_uids;

                $in['intro'] = trim($_POST['intro']);
                $condition['name'] = $in['name'];
                $condition['module_id'] = $in['module_id'];
                $condition['where'][] = "id != $id";
                $c = table('interface')->count($condition);
                if ($c > 0)
                {
                    \Swoole\JS::js_goto("操作失败,该模块下已经存在{$in['name']}接口", "/setting/add_interface/?id={$id}");
                }
                else
                {
                    $res = table('interface')->set($id, $in);
                    if ($res)
                    {
                        \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} modified interface {$id} with " . print_r($in, 1));
                        $this->_save_interface($id, $in);
                        \Swoole\JS::js_goto("操作成功", "/setting/add_interface/?id={$id}");
                    }
                    else
                    {
                        \Swoole\JS::js_goto("操作失败", "/setting/add_interface/?id={$id}");
                    }
                }
                $this->assign('data', $params);
            }
        }
        $this->assign('form', $form);
        $this->display();
    }

    function delete_interface()
    {
        //\Swoole\Error::dbd();
        if (empty($_GET['id']))
        {
            $return['status'] = 400;
            $return['msg'] = '缺少ID参数';
            return json_encode($return);
        }
        $id = (int)$_GET['id'];
        //接口创建人，超级管理员，项目负责人可以删除
        if (!$this->isAllow(__METHOD__, $id))
        {
            $return['status'] = 401;
            $return['msg'] = '没有权限删除';
            return json_encode($return);
        }

        $data = table('interface')->get($id)->get();
        if ($data['owner_uid'] == 0 or $data['owner_uid'] != $this->uid)
        {
            $this->log->put("{$_SESSION['userinfo']['username']} try to del interface {$id} failed cause of owner_uid==0");
            $return['status'] = 300;
            $return['msg'] = '暂时不能删除';
        }
        $res = table('interface')->del($id);
        if ($res)
        {
            \Swoole::$php->log->put("{$_SESSION['userinfo']['username']}  del interface {$id} success " . print_r($data, 1));
            if (\Swoole::$php->redis->delete($this->prefix . "::" . $id) == 1)
            {
                \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} del from redis hash $this->prefix::$id success ");
            }
            else
            {
                \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} del from redis hash $this->prefix::$id failed ");
            }

            if (\Swoole::$php->redis->sRemove($this->prefix, $id) == 1)
            {
                \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} del from redis set {$id} success ");
            }
            else
            {
                \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} del from redis set {$id} failed ");
            }

            $return['status'] = 0;
            $return['msg'] = '操作成功';
        }
        else
        {
            \Swoole::$php->log->put("{$_SESSION['userinfo']['username']}  del interface {$id} failed with db error");
            $return['status'] = 500;
            $return['msg'] = '操作失败';
        }
        return json_encode($return);
    }

    /**
     * 删除模块
     * @return string
     */
    function delete_module()
    {
        if (empty($_GET['id']))
        {
            $return['status'] = 400;
            $return['msg'] = '缺少ID参数';
            return json_encode($return);
        }
        $id = (int)$_GET['id'];
        //接口创建人，超级管理员，项目负责人可以删除
        if (!$this->isAllow(__METHOD__, $id))
        {
            $return['status'] = 400;
            $return['msg'] = '没有权限删除';
            return json_encode($return);
        }

        $data = table('module')->get($id)->get();
        if ($data['owner_uid'] == 0 or $data['owner_uid'] != $this->uid)
        {
            $this->log->put("{$_SESSION['userinfo']['username']} try to del interface {$id} failed cause of owner_uid==0");
            $return['status'] = 300;
            $return['msg'] = '暂时不能删除';
        }
        else
        {
            $res = table('module')->del($id);
            if ($res)
            {
                $return['status'] = 0;
                $return['msg'] = '操作成功';
            }
            else
            {
                $this->log->put("{$_SESSION['userinfo']['username']}  del interface {$id} failed with db error");
                $return['status'] = 500;
                $return['msg'] = '操作失败';
            }
        }

        return json_encode($return);
    }

    function interface_list()
    {
        //Swoole\Error::dbd();
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
        $users['select'] = 'id,username,realname';
        $tmp = table('user', 'platform')->gets($users);
        $user = array();
        foreach ($tmp as $t)
        {
            $name = !empty($t['realname'])?$t['realname']:'';
            $user[$t['id']] = "{$name} [{$t['username']}]";
        }

        $gets['page'] = !empty($_GET['page'])?$_GET['page']:1;
        $gets['pagesize'] = 20;
        $gets['order'] = 'id desc';
        $data = table('interface')->gets($gets,$pager);
        foreach ($data as $k => $v)
        {
            if ($v['owner_uid'] > 0)
            {
                $data[$k]['owner_uid_name'] = $user[$v['owner_uid']];
            }
            else
            {
                $data[$k]['owner_uid_name'] = '';
            }
        }
        $this->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
        $this->assign('data', $data);
        $this->display();
    }

    function list_data()
    {
        $gets = array();
        $gets['select'] = 'id, name,alias,succ_hold,wave_hold, owner_name, addtime';
        $gets['order'] = 'id desc';
        $data = table('interface')->gets($gets);
        if (!empty($data))
        {
            $ret['list'] = $data;
            $ret['status'] = 0;

        } else {
            $ret['list'] = array();
            $ret['status'] = 1;
        }
        return json_encode($ret);
    }

    function add_module()
    {
        //\Swoole\Error::dbd();
        if (empty($_GET['id']) and empty($_POST))
        {
            $gets['select'] = 'id,username,realname';
            $tmp = table('user', 'platform')->gets($gets);
            $user = array();
            foreach ($tmp as $t)
            {
                $name = !empty($t['realname'])?$t['realname']:'';
                $user[$t['id']] = "{$name} [{$t['username']}]";
            }
            $form['name'] = \Swoole\Form::input('name');
            $form['intro'] = \Swoole\Form::text('intro');
            $form['owner_uid'] = \Swoole\Form::select('owner_uid',$user,'',null,array('class'=>'select2 select2-offscreen','style'=>"width:100%" ));
            $form['backup_uids'] = \Swoole\Form::muti_select('backup_uids[]',$user,array(),null,array('class'=>'select2 select2-offscreen','multiple'=>"1",'style'=>"width:100%" ),false);
            $form['alert_uids'] = \Swoole\Form::muti_select('alert_uids[]',$user,array(),null,array('class'=>'select2 select2-offscreen','multiple'=>"1",'style'=>"width:100%" ),false);

            $tmp = table('project', 'platform')->gets(array("order"=>"id desc"));
            $project = array();
            foreach ($tmp as $t)
            {
                $project[$t['id']] = $t['name'];
            }
            $form['project_id'] = \Swoole\Form::select('project_id',$project,'',null,array('class'=>'select2 select2-offscreen','style'=>"width:100%" ));
            $this->assign('form', $form);
            $this->display();
        }
        elseif (!empty($_GET['id']) and empty($_POST))
        {
            $id = (int)$_GET['id'];
            $module = table("module")->get($id)->get();
            $gets['select'] = '*';
            $tmp = table('user', 'platform')->gets($gets);
            $user = array();
            foreach ($tmp as $t)
            {
                $name = !empty($t['realname'])?$t['realname']:'';
                $user[$t['id']] = "{$name} [{$t['username']}]";
            }
            $form['id'] = \Swoole\Form::hidden('id',$module['id']);
            $form['name'] = \Swoole\Form::input('name',$module['name']);
            $form['intro'] = \Swoole\Form::text('intro',$module['intro']);
            $form['owner_uid'] = \Swoole\Form::select('owner_uid',$user,$module['owner_uid'],null,array('class'=>'select2 select2-offscreen','style'=>"width:100%" ));
            $form['backup_uids'] = \Swoole\Form::muti_select('backup_uids[]',$user,explode(',',$module['backup_uids']),null,array('class'=>'select2 select2-offscreen','multiple'=>"1",'style'=>"width:100%" ),false);
            $form['alert_uids'] = \Swoole\Form::muti_select('alert_uids[]',$user,explode(',',$module['alert_uids']),null,array('class'=>'select2 select2-offscreen','multiple'=>"1",'style'=>"width:100%" ),false);

            $tmp = table('project', 'platform')->gets(array("order"=>"id desc"));
            $project = array();
            foreach ($tmp as $t)
            {
                $project[$t['id']] = $t['name'];
            }
            $form['project_id'] = \Swoole\Form::select('project_id',$project,$module['project_id'],null,array('class'=>'select2 select2-offscreen','style'=>"width:100%" ));
            $this->assign('form', $form);
            $this->assign('data', $module);
            $this->display();
        }
        elseif (!empty($_POST) and empty($_POST['id']))
        {
            $in['name'] = trim($_POST['name']);
            $in['owner_uid'] = trim($_POST['owner_uid']);
            if (empty($in['owner_uid']))
            {
                $in['owner_uid'] = $this->uid;
            }
            $in['project_id'] = trim($_POST['project_id']);
            $backup_uids = '';
            if (!empty($_POST['backup_uids']))
            {
                $backup_uids = implode(',',$_POST['backup_uids']);
            }
            $in['backup_uids'] = $backup_uids;

//            $alert_uids = '';
//            if (!empty($_POST['alert_uids']))
//            {
//                $alert_uids = implode(',', $_POST['alert_uids']);
//            }
//            $in['alert_uids'] = $alert_uids;

            $in['succ_hold'] = trim($_POST['succ_hold']);
            $in['wave_hold'] = trim($_POST['wave_hold']);
            $in['alert_int'] = trim($_POST['alert_int']);
            $in['enable_alert'] = trim($_POST['enable_alert']);
            $in['intro'] = trim($_POST['intro']);

            $c = table('module')->count(array('name' => $in['name']));
            if ($c > 0)
            {
                \Swoole\JS::js_goto("操作失败,已存在同名模块","/setting/add_module/");
            }
            else
            {
                $id = table('module')->put($in);
                if ($id)
                {
                    //保存设置模块下所有接口的报警策略
                    $in['module_id'] = $id;
                    $this->_save_module($id, $in);
                    \Swoole\JS::js_goto("操作成功","/setting/add_module/");
                    \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} add module success $id : ". print_r($in,1));
                }
                else
                {
                    \Swoole\JS::js_goto("操作失败","/setting/add_module/");
                    \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} add module failed:  ". print_r($in,1));
                }
            }
        }
        else
        {
            $id = (int)$_POST['id'];
            $in['name'] = trim($_POST['name']);
            $in['owner_uid'] = trim($_POST['owner_uid']);
            $in['project_id'] = trim($_POST['project_id']);
            $backup_uids = '';
            if (!empty($_POST['backup_uids']))
            {
                $backup_uids = implode(',',$_POST['backup_uids']);
            }
            $in['backup_uids'] = $backup_uids;
//            $alert_uids = '';
//            if (!empty($_POST['alert_uids']))
//            {
//                $alert_uids = implode(',', $_POST['alert_uids']);
//            }
//            $in['alert_uids'] = $alert_uids;
            $in['succ_hold'] = trim($_POST['succ_hold']);
            $in['wave_hold'] = trim($_POST['wave_hold']);
            $in['wave_hold'] = trim($_POST['wave_hold']);
            $in['alert_int'] = trim($_POST['alert_int']);
            $in['enable_alert'] = trim($_POST['enable_alert']);
            $in['intro'] = trim($_POST['intro']);
            $where['name'] = $in['name'];
            $where['where'][] = "id !=$id";
            $c = table('module')->count($where);
            if ($c > 0)
            {
                \Swoole\JS::js_goto("操作失败,已存在同名模块","/setting/module_list/");
            }
            else
            {

                $res = table('module')->set($id,$in);
                if ($res)
                {
                    $in['module_id'] = $id;
                    $this->_save_module($id, $in);
                    \Swoole\JS::js_goto("操作成功","/setting/module_list/");
                    \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} modify module success $id : ". print_r($in,1));
                }
                else
                {
                    \Swoole\JS::js_goto("操作失败","/setting/module_list/");
                    \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} modify module failed:  ". print_r($in,1));
                }
            }
        }
    }

    //判断符合包就那报警条件的数据  转存入redis
    function _save_interface($id,$interface)
    {
        //添加到报警集合
        \Swoole::$php->redis->sAdd($this->prefix, $id);
        \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} add redis : interface_id-{$id}");
    }

    //判断符合包就那报警条件的数据  转存入redis
    function _save_module($id,$module)
    {
        $params = array();
        if (  ($module['succ_hold'] > 0 or $module['wave_hold'] > 0)
            and (!empty($module['backup_uids']) or !empty($module['owner_uid'])) and  $module['alert_int'] > 0
        )
        {
            $alert_ids = '';
            if (!empty($module['backup_uids'])) {
                $alert_ids = $module['backup_uids'];
            }
            if (!empty($module['owner_uid'])) {
                $alert_ids .= ",".$module['owner_uid'];
            }
            $params['module_id'] = $id;
            $params['module_name'] = $module['name'];
            $gets['select'] = 'id,mobile,weixinid,username';
            $gets['where'][] = 'id in ('.$alert_ids.')';
            $tmp = table('user', 'platform')->gets($gets);
            $user = array();
            $weixin = array();
            foreach ($tmp as $t)
            {
                if (!empty($t['mobile']))
                {
                    $user[$t['id']] = $t['mobile'];
                }
                if (!empty($t['weixinid']))
                {
                    $weixin[$t['id']] = $t['username'];
                }
            }

            $params['module_id'] = $id;
            $params['module_name'] = $module['name'];
            $params['enable_alert'] = $module['enable_alert'];
            $params['alert_uids'] = $alert_ids;
            $params['alert_mobiles'] = implode(',',$user);
            $params['alert_weixins'] = implode('|',$weixin);
            $params['alert_int'] = $module['alert_int'];
            $params['succ_hold'] = $module['succ_hold'];
            $params['wave_hold'] = $module['wave_hold'];

            $res = table('interface')->gets(array('module_id'=>$id));
            foreach ($res as $re)
            {

                \Swoole::$php->redis->sAdd($this->prefix, $re['id']);//添加接口集合
            }
            $key = $this->prefix."::MODULE::".$id;
            \Swoole::$php->redis->hMset($key, $params);
            \Swoole::$php->log->trace("{$_SESSION['userinfo']['username']}  redis : module-{$id} key-{$key} ". print_r($params,1));
        }
    }

    function module_list()
    {
        //Swoole\Error::dbd();
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

        $users['select'] = '*';
        $tmp = table('user', 'platform')->gets($users);
        $user = array();
        foreach ($tmp as $t)
        {
            $name = !empty($t['realname']) ? $t['realname'] : '';
            $user[$t['id']] = "{$name} [{$t['username']}]";
        }
        if (!empty($_GET['project']))
        {
            $gets['project_id'] = intval($_GET['project']);
        }
        else
        {
            $gets['project_id'] = $this->projectId;
        }
        $gets['page'] = !empty($_GET['page'])?$_GET['page']:1;
        $gets['pagesize'] = 20;
        $gets['order'] = 'id desc';
        $data = table('module')->gets($gets, $pager);
        foreach ($data as $k => $v)
        {
            $back_names = array();
            if (!empty($v['backup_uids']))
            {
                $back_ids = explode(',',$v['backup_uids']);
                foreach($back_ids as $bid)
                {
                    $back_names[] = $user[$bid];
                }
            }
            $data[$k]['backup_uids_name'] = implode(',',$back_names);
            $data[$k]['owner_uid_name'] = $user[$v['owner_uid']];
        }
        $this->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 重置密码
     * @return string
     * @throws \Exception
     */
    function reset_password()
    {
        //不是超级用户不能查看修改用户
        if ($this->userinfo['usertype'] != 0)
        {
            return "access deny";
        }

        if (empty($_GET['id']))
        {
            return \Swoole\JS::js_back("操作不合法");
        }

        $uid = intval($_GET['id']);
        $user = table('user', 'platform')->get($uid);
        if (!$user->exist())
        {
            return \Swoole\JS::js_back("用户不存在");
        }

        $defaultPassword = self::DEFAULT_PASSWORD;
        $user->password = Swoole\Auth::makePasswordHash($user->username, $defaultPassword);
        $user->git_password = $this->getGitPassword($defaultPassword);

        //同步到内网平台
        $this->syncIntranet($user->username, ['git_password' => $user->git_password]);

        if ($user->save())
        {
            return \Swoole\JS::js_goto("重置{$user->username}登录密码成功", '/setting/user_list/');
        }
        else
        {
            return \Swoole\JS::js_goto("重置密码失败，请稍后重试", '/setting/user_list/');
        }
    }


    function user_list()
    {
        //不是超级用户不能查看修改用户
        if ($this->userinfo['usertype'] != 0)
        {
            return "access deny";
        }

        if (!empty($_GET['del']))
        {
            $u = table("user", 'platform')->get(intval($_GET['del']));
            if ($u->delete())
            {
                $this->syncIntranet($u['username'], ['lock' => 1]);
                return Swoole\JS::js_goto("删除成功", '/setting/user_list/');
            }
        }
        elseif (!empty($_GET['block']))
        {
            $blocking = isset($_GET['unblock']) ? 0 : 1;
            $u = table("user", 'platform')->get(intval($_GET['block']));
            if ($this->syncIntranet($u['username'], ['lock' => $blocking]))
            {
                $u->blocking = $blocking;
                if ($u->save())
                {
                    return Swoole\JS::js_goto("操作成功", '/setting/user_list/');
                }
            }
            return Swoole\JS::js_goto("操作失败，请重试！", '/setting/user_list/');
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
        $data = table("user", 'platform')->gets($gets,$pager);
        $this->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
        $this->assign('data', $data);
        $this->display();
    }

    protected function filterPostData(&$data)
    {
        $data['rules'] = empty($_POST['rules']) ? '' : implode($_POST['rules']);
        $data['project_id'] = empty($_POST['project_id']) ? '' : implode($_POST['project_id']);

        $data['realname'] = trim($_POST['realname']);
        $data['username'] = trim($_POST['username']);
        //微信号
        $data['weixinid'] = trim($_POST['weixinid']);
        //手机号
        $data['mobile'] = trim($_POST['mobile']);
        // NOTE: 写死0，貌似目前没用到
        $data['uid'] = 0;
        $data['usertype'] = (int)$_POST['usertype'];
    }

    function add_user()
    {
        //不是超级用户不能查看修改用户
        if ($this->userinfo['usertype'] != 0)
        {
            return "access deny";
        }
        //\Swoole::$php->db->debug = true;
        if (empty($_GET) and empty($_POST))
        {
            $tmp = table('project', 'platform')->gets(array("order"=>"id desc"));
            $project = array();
            foreach ($tmp as $t)
            {
                $project[$t['id']] = $t['name'];
            }
            $form['project_id'] = \Swoole\Form::muti_select('project_id[]', $project, array(), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
            $form['rules'] = \Swoole\Form::muti_select('rules[]', self::$roles, [], null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
            $form['uid'] = \Swoole\Form::input('uid');
            $form['mobile'] = \Swoole\Form::input('mobile');
            $form['realname'] = \Swoole\Form::input('realname');
            $form['username'] = \Swoole\Form::input('username');
            $form['weixinid'] = \Swoole\Form::input('weixinid');
            $form['usertype'] = \Swoole\Form::select('usertype', $this->config['usertype'], null, null, array('class' => 'select2'));
            $this->assign('form', $form);
            $this->display();
        }
        elseif (!empty($_GET['id']) and empty($_POST))
        {
            $id = (int)$_GET['id'];
            $user = table('user', 'platform')->get($id)->get();

            $tmp = table('project', 'platform')->gets(array("order"=>"id desc"));
            $project = array();
            foreach ($tmp as $t)
            {
                $project[$t['id']] = $t['name'];
            }
            $form['project_id'] = \Swoole\Form::muti_select('project_id[]', $project, explode(',', $user['project_id']), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
            $form['rules'] = \Swoole\Form::muti_select('rules[]', self::$roles, explode(',', $user['rules']), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
            $form['uid'] = \Swoole\Form::input('uid', $user['uid']);
            $form['mobile'] = \Swoole\Form::input('mobile', $user['mobile']);
            $form['realname'] = \Swoole\Form::input('realname', $user['realname']);
            $form['username'] = \Swoole\Form::input('username', $user['username']);
            $form['weixinid'] = \Swoole\Form::input('weixinid', $user['weixinid']);
            $form['usertype'] = \Swoole\Form::select('usertype', $this->config['usertype'], $user['usertype'], null, array('class' => 'select2'));
            $form['id'] = \Swoole\Form::hidden('id',$user['id']);
            $this->assign('form', $form);
            $this->display();
        }
        elseif (!empty($_POST['id']))
        {
            $id = (int)$_POST['id'];
            $this->filterPostData($inserts);
            $res = table("user", 'platform')->set($id,$inserts);
            if ($res)
            {
                $this->addWeiXin($inserts);
                $msg = "修改成功";
            }
            else
            {
                $msg = "修改失败";
            }
            \Swoole\JS::js_goto($msg, '/setting/user_list/');
        }
        else
        {
            $inserts['username'] = trim($_POST['username']);
            if (table('user', 'platform')->exists($inserts))
            {
                \Swoole\JS::js_goto("账户已存在",'/setting/user_list/');
                return;
            }
            $this->filterPostData($inserts);
            $inserts['uid'] = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
            $inserts['gid'] = 0;
            $inserts['code_type'] = 0;
            $inserts['last_ip'] = '';
            $inserts['last_time'] = 0;
            $inserts['blocking'] = 0;
            $inserts['svn_password'] = '';
            //默认密码
            $inserts['password'] = Swoole\Auth::makePasswordHash($inserts['username'], self::DEFAULT_PASSWORD);
            $inserts['git_password'] = $this->getGitPassword(self::DEFAULT_PASSWORD);
            $newUser = [
                'username' => $inserts['username'],
                'git_password' => $inserts['git_password'],
                'fullname' => $inserts['realname'],
                'phone' => $inserts['mobile'],
            ];
            $newUser['git'] = empty($_POST['git_account']) ? 0 : 1;
            //同步到内网平台
            if (!$this->syncIntranet('', $newUser))
            {
                goto fail;
            }

            $res = table("user", 'platform')->put($inserts);
            if ($res)
            {
                $this->addWeiXin($inserts);
                \Swoole\JS::js_goto("添加成功", '/setting/user_list/');
            }
            else
            {
                fail:
                \Swoole\JS::js_goto("添加失败，请稍后重试", '/setting/user_list/');
            }
        }
    }
}