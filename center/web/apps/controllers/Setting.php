<?php
namespace App\Controller;
use Ddl\User;
use mobilemsg\service\Filter;
use Swoole;
use App;

/**
 * 系统管理，只有超级管理员有权限
 * @package App\Controller
 */
class Setting extends App\LoginController
{
    protected $prefix = 'YYPUSH';

    public $alert_types = [
        1 => "谈窗",
        2 => '短信'
    ];

    static $roles = [
        'app' => '客户端控制',
        'stats' => '模调统计',
        'url' => '短链接系统',
        'common_admin' => 'common后台管理员',
        'sms' => '短信管理',
    ];

    static $app_enable = [
        1 => '开启',
        2 => '关闭'
    ];

    static $app_has_init = [
        1 => '已初始化',
        2 => '未初始化'
    ];

    static $app_upload_dir = [
        1 => '/data/msg_push/ios_certification/',
        2 => '/data/msg_push/android_certification/',
    ];

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
        $data = table('module')->get($id)->get();

        //接口创建人，超级管理员，项目负责人可以删除
        if (!$this->isAllow(__METHOD__, $id) and ($data['owner_uid'] == 0 or $data['owner_uid'] != $this->uid))
        {
            $return['status'] = 400;
            $return['msg'] = '没有权限删除';
            return json_encode($return);
        }

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
            unset($_GET['page']);
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
        if (!empty($_POST['module_id']))
        {
            $gets['module_id'] = intval($_POST['module_id']);
            $_GET['module_id'] = $gets['module_id'];
        }
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
            $in['project_id'] = $this->projectId;

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
    function _save_interface($id, $interface)
    {
        //添加到报警集合
        \Swoole::$php->redis->sAdd($this->prefix, $id);
        \Swoole::$php->log->put("{$_SESSION['userinfo']['username']} add redis : interface_id-{$id}");
    }

    //判断符合包就那报警条件的数据  转存入redis
    function _save_module($id, $module)
    {
        $params = array();
        if (($module['succ_hold'] > 0 or $module['wave_hold'] > 0)
            and (!empty($module['backup_uids']) or !empty($module['owner_uid'])) and $module['alert_int'] > 0
        )
        {
            $alert_ids = '';
            if (!empty($module['backup_uids']))
            {
                $alert_ids = $module['backup_uids'];
            }
            if (!empty($module['owner_uid']))
            {
                $alert_ids .= "," . $module['owner_uid'];
            }

            $params['module_id'] = $id;
            $params['module_name'] = $module['name'];
            $gets['select'] = 'id,mobile,weixinid,username';
            $gets['where'][] = 'id in ('.trim($alert_ids, ',').')';
            $tmp = table('user', 'platform')->gets($gets);
            $user = array();
            $weixin = array();
            $alert = array();
            foreach ($tmp as $t)
            {
                if (!empty($t['mobile']))
                {
                    $user[$t['id']] = $t['mobile'];
                    $alert[$t['id']]['mobile'] = $t['mobile'];
                }
                if (!empty($t['weixinid']))
                {
                    $weixin[$t['id']] = $t['username'];
                    $alert[$t['id']]['weixinid'] = $t['username'];
                }
            }

            $params['module_id'] = $id;
            $params['module_name'] = $module['name'];
            $params['enable_alert'] = $module['enable_alert'];
            $params['alert_uids'] = $alert_ids;
            $params['alert_mobiles'] = implode(',',$user);
            $params['alert_weixins'] = implode('|',$weixin);
            $params['alerts'] = json_encode($alert);
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
        $gitPassword = $user->git_password;
        if (!empty($gitPassword))
        {
            $user->git_password = $this->getGitPassword($defaultPassword);
            //同步到内网平台
            if (!$this->syncIntranet($user->username, ['git' => 1, 'git_password' => $user->git_password]))
            {
                goto fail;
            }
        }
        $this->resetLimit($user->username);
        //同步到Confluence
        App\CrowdUser::setPassword($user['username'], self::DEFAULT_PASSWORD);

        if ($user->save())
        {
            return \Swoole\JS::js_goto("重置{$user->username}登录密码成功", '/setting/user_list/');
        }
        else
        {
            fail:
            return \Swoole\JS::js_goto("重置密码失败，请稍后重试", '/setting/user_list/');
        }
    }

    protected function  resetLimit($username)
    {
        $limit_key = 'App\Controller:Employee:' . $username . ':password_error';
        $this->limit->reset($limit_key);
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
            return Swoole\JS::js_goto("操作失败，请重试！\\n错误信息：".$this->errMsg."\\n错误码：".$this->errCode, '/setting/user_list/');
        }
        elseif (!empty($_GET['reset_limit']))
        {
            $u = table("user", 'platform')->get(intval($_GET['reset_limit']));
            $this->resetLimit($u->username);
            return Swoole\JS::js_back("操作成功");
        }

        $gets = array();
        if (!empty($_GET['uid']))
        {
            $_GET['uid'] = intval(trim($_GET['uid']));
            $gets['uid'] = $_GET['uid'];
        }
        if (!empty($_GET['username']))
        {
            $_GET['username'] = trim($_GET['username']);
            $gets['where'][] = "username like '%{$_GET['username']}%'";
        }
        if (!empty($_GET['realname']))
        {
            $_GET['realname'] = trim($_GET['realname']);
            $gets['where'][] = "realname like '%{$_GET['realname']}%'";
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
        $data['rules'] = empty($_POST['rules']) ? '' : implode(',', $_POST['rules']);
        $data['project_id'] = empty($_POST['project_id']) ? '' : implode(',', $_POST['project_id']);

        $data['realname'] = trim($_POST['realname']);
        $data['username'] = trim($_POST['username']);
        //微信号
        $data['weixinid'] = trim($_POST['weixinid']);
        //钉钉ID
        $data['dingdingid'] = trim($_POST['dingdingid']);
        //手机号
        $data['mobile'] = trim($_POST['mobile']);
        // NOTE: 写死0，貌似目前没用到
        $data['uid'] = 0;
        $data['usertype'] = (int)$_POST['usertype'];
    }

    protected function getUserForm($user = [])
    {
        $tmp = table('project', 'platform')->gets(array("order" => "id desc"));
        $projects = array();
        foreach ($tmp as $t)
        {
            $projects[$t['id']] = $t['name'];
        }

        if (empty($user))
        {
            $user['project_id'] = '';
            $user['rules'] = '';
            $user['uid'] = 0;
            $user['id'] = 0;
            $user['mobile'] = '';
            $user['realname'] = '';
            $user['username'] = '';
            $user['weixinid'] = '';
            $user['dingdingid'] = '';
            $user['usertype'] = '2';
            $this->assign('gitAccount', false);
        }
        else
        {
            $this->assign('gitAccount', !empty($user['git_password']));
            $crowdUser = App\CrowdUser::get($user['username']);
            $this->assign('crowdUser', $crowdUser);
        }

        // var_dump($form['rules'] = Swoole\Form::muti_select('rules[]', self::$roles, explode(',', $user['rules']), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false));
        // exit;

        $form['project_id'] = Swoole\Form::muti_select('project_id[]', $projects, explode(',', $user['project_id']), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
        $form['rules'] = Swoole\Form::muti_select('rules[]', self::$roles, explode(',', $user['rules']), null, array('class' => 'select2 select2-offscreen', 'multiple' => "1", 'style' => "width:100%"), false);
        $form['uid'] = Swoole\Form::input('uid', $user['uid']);
        $form['mobile'] = Swoole\Form::input('mobile', $user['mobile']);
        $form['realname'] = Swoole\Form::input('realname', $user['realname']);
        $form['username'] = Swoole\Form::input('username', $user['username']);
        $form['weixinid'] = Swoole\Form::input('weixinid', $user['weixinid']);
        $form['dingdingid'] = Swoole\Form::input('dingdingid', $user['dingdingid']);
        $form['usertype'] = Swoole\Form::select('usertype', $this->config['usertype'], $user['usertype'], null, array('class' => 'select2'));
        $form['id'] = Swoole\Form::hidden('id', $user['id']);
        return $form;
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
            $form = $this->getUserForm();
            $this->assign('form', $form);
            $this->display();
        }
        elseif (!empty($_GET['id']) and empty($_POST))
        {
            $id = (int)$_GET['id'];
            $user = table('user', 'platform')->get($id)->get();
            $form = $this->getUserForm($user);
            $this->assign('form', $form);
            $this->display();
        }
        elseif (!empty($_POST['id']))
        {
            $id = (int)$_POST['id'];

            $inserts = [];
            $this->filterPostData($inserts);
            //同步到内网平台
            $user = table('user', 'platform')->get($id)->get();

            $gitAccount = empty($_POST['git_account']) ? 0 : 1;
            if ($gitAccount)
            {
                if (empty($user['git_password']))
                {
                    $inserts['git_password'] = $update['git_password'] = $this->getGitPassword(self::DEFAULT_PASSWORD);
                    $update['git'] = 1;
                }
            }
            else
            {
                $inserts['git_password'] = '';
            }

            //创建Doc账户
            if (!empty($_POST['crowd_user']))
            {
                App\CrowdUser::newAccount($inserts['username'], $inserts['realname'], self::DEFAULT_PASSWORD);
            }

            $update['phone'] = $inserts['mobile'];
            $update['fullname'] = $inserts['realname'];

            $this->syncIntranet($user['username'], $update);

            $res = table("user", 'platform')->set($id, $inserts);
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
            $inserts['md5_password'] = '';
            $inserts['property'] = '';
            //默认密码
            $inserts['password'] = Swoole\Auth::makePasswordHash($inserts['username'], self::DEFAULT_PASSWORD);

            $newUser = [
                'username' => $inserts['username'],
                'fullname' => $inserts['realname'],
                'phone' => $inserts['mobile'],

            ];

            $gitAccount = empty($_POST['git_account']) ? 0 : 1;
            if ($gitAccount)
            {
                $newUser['git_password'] = $inserts['git_password'] = $this->getGitPassword(self::DEFAULT_PASSWORD);
            }
            else
            {
                $inserts['git_password'] = '';
            }

            $newUser['git'] = $gitAccount;

            //同步到内网平台
            if (!$this->syncIntranet('', $newUser))
            {
                goto fail;
            }

            //同步到Confluence
            App\CrowdUser::newAccount($inserts['username'], $inserts['realname'], self::DEFAULT_PASSWORD);

            $res = table("user", 'platform')->put($inserts);
            if ($res)
            {
                $this->addWeiXin($inserts);
                \Swoole\JS::js_goto("添加成功", '/setting/user_list/');
            }
            else
            {
                fail:
                \Swoole\JS::js_goto("添加失败，请稍后重试.CODE=".$this->errCode."|MSG=".$this->errMsg, '/setting/user_list/');
            }
        }
    }

    function user_property()
    {
        //\Swoole::$php->db->debug = true;
        if (empty($_GET['id']))
        {
            return "错误的请求";
        }
        $id = intval($_GET['id']);
        $user = table('user', 'platform')->get($id)->get();
        $property = empty($user['property']) ? '{}' : $user['property'];
        if (!empty($_POST['json']))
        {
            Swoole\Filter::safe($_POST['json']);
            if (table('user', 'platform')->set($id, ['property' => $_POST['json']]))
            {
                return $this->json('');
            }
            else
            {
                return $this->json('err', 500);
            }
        }
        else
        {
            $this->assign('json', $property);
            $this->display();
        }
    }

    function delete_project()
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
        if (table('project', 'platform')->del($id))
        {
            return Swoole\JS::js_back("删除成功");
        }
        else
        {
            return Swoole\JS::js_back("删除失败");
        }
    }

    function add_project()
    {
        if (!empty($_POST['name']))
        {
            $inserts['name'] = trim($_POST['name']);
            $inserts['intro'] = trim($_POST['intro']);
            $inserts['ckey'] = trim($_POST['ckey']);

            $update = !empty($_POST['id']);

            if ($inserts['ckey'] !== '')
            {
                $inserts['ckey'] = strtolower($inserts['ckey']);

                $project_table = table('project', 'platform');

                if ($update)
                {
                    $params = [
                        'where' => sprintf("ckey = '%s' AND id != %d", $project_table->db->quote($inserts['ckey']), intval($_POST['id'])),
                    ];
                }
                else
                {
                    $params = [
                        'where' => sprintf("ckey = '%s'", $project_table->db->quote($inserts['ckey'])),
                    ];
                }

                $ckey_exists = (bool) $project_table->count($params);
            }

            if (!$ckey_exists)
            {
                $msg['code'] = 0;
                if (!$update)
                {
                    $res = table('project', 'platform')->put($inserts);
                    $msg['message'] = $res ? "添加成功，ID: " . $res : "添加失败";
                }
                else
                {
                    $res = table('project', 'platform')->set(intval($_POST['id']),$inserts);
                    $msg['message'] = $res ? "修改成功" : "修改失败";
                }
            }
            else
            {
                $msg['code'] = 1;
                $msg['message'] = '已存在同名项目代号！';
            }

            $this->assign('msg', $msg);
        }

        if (empty($_GET))
        {
            $res = [];
            $form['name'] = \Swoole\Form::input('name');
            $form['intro'] = \Swoole\Form::text('intro');
            $form['ckey'] = \Swoole\Form::input('ckey');
        }
        else
        {
            $id = (int)$_GET['id'];
            $res = table('project', 'platform')->get($id)->get();
            $form['name'] = \Swoole\Form::input('name', $res['name']);
            $form['intro'] = \Swoole\Form::text('intro', $res['intro']);
            $form['ckey'] = \Swoole\Form::input('ckey', $res['ckey']);
            $form['id'] = \Swoole\Form::hidden('id', $res['id']);
        }
        $this->assign('pro', $res);
        $this->assign('form', $form);
        $this->display();
    }

    function project_list()
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
        $data = table('project', 'platform')->gets($gets,$pager);
        $this->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
        $this->assign('data', $data);
        $this->display();
    }

	function alert_project() {
		//不是超级用户不能查看修改用户
		if ($this->userinfo['usertype'] != 0) {
			return "access deny";
		}

		$pid = empty($_GET['id']) ? 0 : intval($_GET['id']);
		$m = table('project_alert', 'platform');
		$users = array_rebuild($m->db->query("select * from user")->fetchall(), 'uid', 'username');

		$selected = array_rebuild($m->db->query("select * from project_alert where `pid`='$pid'")->fetchall(), 'id', 'uid');
		if ($_POST) {
			if (empty($_POST['uids'])) {
				$_POST['uids'] = [];
			}
			foreach (array_diff($_POST['uids'], array_values($selected)) as $uid) {
				$m->put(array(
					'uid' => $uid,
					'pid' => $pid
				));
			}
			if ($dels = array_diff(array_values($selected), $_POST['uids'])) {
				$m->db->query("delete from `project_alert` where `pid`='$pid' and `uid` in (" . implode(',', $dels) . ")");
			}
			header("location:/setting/project_list");
			exit;
		}

		$form = array();
		$form['users'] = \Swoole\Form::muti_select('uids[]', $users, $selected, null, array(
			'class' => 'select2 select2-offscreen',
			'multiple' => "1",
			'style' => "width:100%"
		), false);
		$this->assign('form', $form);
		$this->display();
	}

    function app_project_list()
    {
        $params = [
            'page' => intval(array_get($_GET, 'page', 1)),
            'pagesize' => 15,
            'order' => 'id desc',
        ];
        $data = table('app_project', 'platform')->gets($params, $pager);

        $this->assign('page_title', 'APP项目管理');
        $this->assign('data', $data);
        $this->assign('pager', $pager);
        $this->display();
    }

    function add_app_project()
    {
        if (!empty($_POST))
        {
            $form_data = $_POST;
            $data = $this->validate($form_data, [$this, 'editAppProjectCheck'], $errors);
            if (empty($errors))
            {
                $data['create_time'] = $data['update_time'] = date('Y-m-d H:i:s');
                $insert_id = table('app_project', 'platform')->put($data);
                if ($insert_id)
                {
                    \App\Session::flash('msg', '添加APP项目成功！');
                    return $this->redirect("/setting/edit_app_project?id={$insert_id}");
                }
                else
                {
                    $errors[] = '添加失败，请联系管理员！';
                }
            }
        }

        $this->assign('page_title', '新增APP项目');
        $this->assign('form_data', !empty($form_data) ? $form_data : []);
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display('setting/edit_app_project.php');
    }

    function edit_app_project()
    {
        $id = !empty($_GET['id']) ? intval($_GET['id']) : null;
        if (!is_null($id))
        {
            $app_project = table('app_project', 'platform')->get($id);
        }
        if (empty($app_project))
        {
            return $this->error('APP项目不存在！');
        }

        if (!empty($_POST))
        {
            $form_data = $_POST;
            $form_data['app_project_id'] = $id;
            $data = $this->validate($form_data, [$this, 'editAppProjectCheck'], $errors);
            if (empty($errors))
            {
                $db_data['update_time'] = date('Y-m-d H:i:s');
                $result = table('app_project', 'platform')->set($id, $data);
                if ($result)
                {
                    \App\Session::flash('msg', '编辑APP项目成功！');
                    return $this->redirect("/setting/edit_app_project?id={$id}");
                }
                else
                {
                    $errors[] = '编辑失败，请联系管理员！';
                }
            }
        }
        else
        {
            $form_data = $app_project;
        }

        $this->assign('page_title', '编辑APP项目');
        $this->assign('form_data', !empty($form_data) ? $form_data : []);
        $this->assign('msg', \App\Session::get('msg'));
        $this->assign('errors', !empty($errors) ? $errors : []);
        $this->display('setting/edit_app_project.php');
    }

    function delete_app_project()
    {
        $id = intval(array_get($_GET, 'id'));
        if (!empty($id))
        {
            $result = table('app_project', 'platform')->del($id);
            if (!$result)
            {
                return $this->error('DB错误，请联系管理员！');
            }
        }
        return $this->success('操作成功！', '/setting/app_project_list');
    }

    function app_list()
    {
        $uid = $_SESSION['user_id'];
        //$gets['uids'] = $uid;
        if (!empty($_POST['name']))
        {
            $name = trim($_POST['name']);
            $gets['where'][] = "name like '%$name%'";;
        }

        $gets['page'] = !empty($_GET['page'])?$_GET['page']:1;
        $gets['pageSize'] = 15;
        $gets['order'] = 'enable asc, is_inited asc, id desc';
        //\Swoole::$php->db->debug = 1;
        $data = table('app', 'platform')->gets($gets,$pager);

        $os_list = $this->get_app_os_list();
        foreach ($data as $k => $v)
        {
            $data[$k]['os_name'] = $os_list[$v['os']];
            $data[$k]['enable_name'] = self::$app_enable[$v['enable']];
            $cert_info = \Swoole::$php->redis->hGetAll($v['app_key']."_".$v['os']);
            $has_init = 2;
            if (!empty($cert_info)
                && ($cert_info['os'] == 1)
                && isset($cert_info['apns_pem_file'])
                && is_file($cert_info['apns_pem_file'])) {
                $has_init = 1;
            }
            if (!empty($cert_info)
                && ($cert_info['os'] == 2)
                && isset($cert_info['umeng_pem_file'])
                && isset($cert_info['umeng_key_file'])
                && is_file($cert_info['umeng_pem_file'])
                && is_file($cert_info['umeng_key_file'])) {
                $has_init = 1;
            }
            $data[$k]['has_init_name'] = self::$app_has_init[$has_init];
            $data[$k]['has_init'] = $has_init;
        }

        $this->assign('pager', array('total'=>$pager->total,'render'=>$pager->render()));
        $form['name'] = \Swoole\Form::input('name',isset($_POST['name']) ? $_POST['name'] : '',array('class'=>'form-control input-sm',
                                                                        'placeholder'=>"APP名称"));

        $this->assign('form', $form);
        $this->assign('data', $data);
        $this->display();
    }

    function add_app()
    {
        $errors = [];
        $form_data['os_list'] = $this->get_app_os_list();

        if (empty($_POST))
        {
            $form_data['enable'] = \Swoole::$php->config['setting']['app_default_status'];
        }
        else
        {
            $form_data = array_merge($form_data, $_POST);
            $db_data = $this->validate($form_data, [$this, 'editAppCheck'], $errors);

            if (empty($errors))
            {
                $db_data['create_time'] = $db_data['update_time'] = date('Y-m-d H:i:s');
                $insert_id = table('app', 'platform')->put($db_data);
                if ($insert_id)
                {
                    \App\Session::flash('msg', '添加APP成功！');
                    return $this->http->header('Location', "/setting/edit_app?id={$insert_id}");
                }
                else
                {
                    $errors[] = '添加失败，请联系管理员！';
                }
            }
        }

        $this->assign('page_title', '添加APP');
        $this->assign('errors', $errors);
        $this->assign('form_data', $form_data);
        $this->display('setting/edit_app.php');
    }

    function edit_app()
    {
        $id = !empty($_GET['id']) ? intval($_GET['id']) : null;
        if (!is_null($id))
        {
            $app = table('app', 'platform')->get($id)->get();
        }
        if (empty($app))
        {
            return $this->error('APP不存在！');
        }

        $errors = [];
        $form_data['app_id'] = $id;
        $form_data['os_list'] = $this->get_app_os_list();

        if (empty($_POST))
        {
            $form_data = array_merge($form_data, $app);
        }
        else
        {
            $form_data = array_merge($form_data, $_POST);
            $db_data = $this->validate($form_data, [$this, 'editAppCheck'], $errors);

            if (empty($errors))
            {
                $db_data['update_time'] = date('Y-m-d H:i:s');
                $result = table('app', 'platform')->set($id, $db_data);
                if ($result)
                {
                    \App\Session::flash('msg', '编辑APP成功！');
                    return $this->redirect("/setting/edit_app?id={$id}");
                }
                else
                {
                    $errors[] = '编辑失败，请联系管理员！';
                }
            }
        }

        $this->assign('page_title', '编辑APP');
        $this->assign('msg', \App\Session::get('msg'));
        $this->assign('errors', $errors);
        $this->assign('form_data', $form_data);
        $this->display('setting/edit_app.php');
    }

    function app_gen_cert()
    {
        $id = array_get($_POST, 'id');
        if (empty($id))
        {
            return \Swoole\JS::js_back("操作失败,请联系管理员");
        }
        $app = table('project', 'platform')->get($id)->get();

        if ($app['os'] == 2) {
            //umeng 证书初始化
            $p12 = $app['umeng_cert'];
            $pwd = $app['umeng_pwd'];

            $file_name = basename($p12,'.p12');
            $dir_name = dirname($p12);
            //key
            exec("openssl pkcs12 -nodes -nocerts -out $dir_name/$file_name.key -in $p12 -passin pass:$pwd",$outpot1);
            \Swoole::$php->log->trace("openssl pkcs12 -nodes -nocerts -out $dir_name/$file_name.key -in $p12 -passin pass:$pwd".var_export($outpot1,1));            //cert
            exec("openssl pkcs12 -nokeys -out $dir_name/$file_name.pem -in $p12 -passin pass:$pwd",$output2);
            \Swoole::$php->log->trace("openssl pkcs12 -nokeys -out $dir_name/$file_name.pem -in $p12 -passin pass:$pwd".var_export($output2,1));
            $app['umeng_pem_file'] = "$dir_name/$file_name.pem";
            $app['umeng_key_file'] = "$dir_name/$file_name.key";
            \Swoole::$php->redis->hMset($app['app_key']."_".$app['os'],$app);
            \Swoole::$php->redis->sAdd(\Swoole::$php->config['redis_key']['app_sets'],$app['app_key']."_".$app['os']);

            $insert['is_inited'] = 1;
            $insert['update_time'] = date("Y-m-d H:i:s");
            table('project', 'platform')->set($id,$insert);
            \Swoole\JS::js_goto("初始化成功", "/setting/edit_app/?id={$id}");
        } elseif ($app['os'] == 1) {
            $p12 = $app['apns_cert'];
            $pwd = $app['apns_pwd'];

            $file_name = basename($p12,'.p12');
            $dir_name = dirname($p12);
            //key
            $key_file = "$dir_name/$file_name.key";
            exec("openssl pkcs12 -nodes -nocerts -out $key_file -in $p12 -passin pass:$pwd",$outpot1);
            \Swoole::$php->log->trace("openssl pkcs12 -nodes -nocerts -out $key_file -in $p12 -passin pass:$pwd".var_export($outpot1,1));
            //cert
            $cert_file = "$dir_name/$file_name.cert";
            exec("openssl pkcs12 -nokeys -out $cert_file -in $p12 -passin pass:$pwd",$output2);
            \Swoole::$php->log->trace("openssl pkcs12 -nokeys -out $cert_file -in $p12 -passin pass:$pwd".var_export($output2,1));
            //合成pem
            exec("cat $key_file $cert_file >$dir_name/$file_name.pem",$output3);
            \Swoole::$php->log->trace("cat $key_file $cert_file >$dir_name/$file_name.pem".var_export($output3,1));
            $app['apns_pem_file'] = "$dir_name/$file_name.pem";
            \Swoole::$php->redis->hMset($app['app_key']."_".$app['os'],$app);
            \Swoole::$php->redis->sAdd(\Swoole::$php->config['redis_key']['app_sets'],$app['app_key']."_".$app['os']);

            $insert['is_inited'] = 1;
            $insert['update_time'] = date("Y-m-d H:i:s");
            table('project', 'platform')->set($id,$insert);
            \Swoole\JS::js_goto("初始化成功", "/setting/edit_app/?id={$id}");
        } else {
            \Swoole\JS::js_back("数据错误,请联系管理员");
        }
    }

    private function get_app_os_list()
    {
        $os_list = \Swoole::$php->config['setting']['app_os'];
        unset($os_list[3]);
        return $os_list;
    }

    protected function editAppProjectCheck(array $input, &$errors)
    {
        $output['name'] = trim(array_get($input, 'name'));
        if ($output['name'] === '')
        {
            $errors[] = 'APP项目名称不能为空！';
        }
        $output['app_key'] = trim(array_get($input, 'app_key'));
        if ($output['app_key'] !== '')
        {
            if (isset($input['app_project_id']))
            {
                $params = [
                    'where' => sprintf("app_key = '%s' AND id != %d", $output['app_key'], $input['app_project_id']),
                ];
            }
            else
            {
                $params = [
                    'where' => sprintf("app_key = '%s'", $output['app_key']),
                ];
            }
            $count = table('app_project', 'platform')->count($params);
            if ($count)
            {
                $errors[] = '已存在同名APP_KEY！';
            }
        }
        else
        {
            $errors[] = 'APP_KEY不能为空！';
        }
        return $output;
    }

    protected function editAppCheck(array $data, &$errors)
    {
        $db_data['name'] = trim(array_get($data, 'name'));
        if ($db_data['name'] === '')
        {
            $errors[] = 'APP名称不能为空！';
        }
        $db_data['package_name'] = trim(array_get($data, 'package_name'));
        if ($db_data['package_name'] === '')
        {
            $errors[] = '包名不能为空！';
        }
        $db_data['app_key'] = trim(array_get($data, 'app_key'));
        if ($db_data['app_key'] === '')
        {
            $errors[] = 'app_key不能为空！';
        }
        $db_data['os'] = intval(array_get($data, 'os'));
        if (!in_array($db_data['os'], array_keys($data['os_list'])))
        {
            $errors[] = 'OS值非法！';
        }
        $db_data['enable'] = intval(array_get($data, 'enable'));
        $enable_status_list = \Swoole::$php->config['setting']['app_enable_status_list'];
        if (!in_array($db_data['enable'], $enable_status_list))
        {
            $errors[] = '项目状态值非法！';
        }
        $db_data['apns_pwd'] = trim(array_get($data, 'apns_pwd'));
        $db_data['umeng_pwd'] = trim(array_get($data, 'umeng_pwd'));

        if (empty($errors))
        {
            if (!empty($data['app_id']))
            {
                $query_params = [
                    'where' => sprintf('os = %d AND (package_name = "%s" OR app_key = "%s") AND id != %d', $db_data['os'], $db_data['package_name'], $data['app_key'], $data['app_id']),
                ];
            }
            else
            {
                $query_params = [
                    'where' => sprintf('os = %d AND (package_name = "%s" OR app_key = "%s")', $db_data['os'], $db_data['package_name'], $data['app_key']),
                ];
            }
            $app_list = table('app', 'platform')->gets($query_params);
            if (!empty($app_list))
            {
                $key_exists = false;
                $package_exists = false;
                foreach ($app_list as $app)
                {
                    if ($app['package_name'] === $db_data['package_name'])
                    {
                        $package_exists = true;
                    }
                    if ($app['app_key'] === $db_data['app_key'])
                    {
                        $key_exists = true;
                    }
                }

                if ($package_exists)
                {
                    $errors[] = sprintf('同一系统(%s)不能存在相同包名(%s)', $data['os_list'][$db_data['os']], $db_data['package_name']);
                }

                if ($key_exists)
                {
                    $errors[] = sprintf('同一系统(%s)不能存在相同的app_key(%s)', $data['os_list'][$db_data['os']], $db_data['app_key']);
                }
            }
        }

        return $db_data;
    }

    function machine()
    {
        if (!empty($_POST['ip']))
        {
            if (!Swoole\Validate::ip($_POST['ip']))
            {
                \Swoole\JS::js_back("错误的IP地址格式");
                return;
            }
            $layer = intval($_POST['layer']);
            if ($layer < 0)
            {
                \Swoole\JS::js_back("请选择分层");
                return;
            }
            $table = table('machine', 'platform');
            $insert['ip'] = trim($_POST['ip']);
            $insert['project_id'] = $this->projectId;
            $insert['layer'] = $_POST['layer'];
            if ($table->count($insert) > 0)
            {
                \Swoole\JS::js_back("IP地址已存在.");
                return;
            }
            $insert['add_uid'] = $this->uid;
            $insert['intro'] = $_POST['intro'];
            $table->put($insert);
            $this->http->redirect("/setting/machine/");
            return;
        }
        else
        {
            $this->display();
        }
    }
}
