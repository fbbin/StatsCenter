<?php
namespace App;

class LoginController extends \Swoole\Controller
{
    protected $uid;
    protected $userinfo;
    protected $projectId;
    protected $projectInfo;

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

        /**
         * 可参与的项目
         */
        $project_ids = $this->userinfo['project_id'];
        if ($project_ids)
        {
            $projects = table('project')->getMap(array('in' => array('id', $project_ids)));
        }
        else
        {
            $projects = table('project')->getMap(array('limit' => 100));
        }

        $this->assign('_projects', $projects);
        //从GET参数中获取ProjectId
        if (!empty($_GET['project']))
        {
            $this->projectId = intval($_GET['project']);
            //此用户受限访问某几个项目，传入GET参数Project不在被允许的范围内
            if ($project_ids and !isset($projects[$this->projectId]))
            {
                goto first_project;
            }
        }
        //从Session中获取
        elseif (!empty($_SESSION['project']))
        {
            $this->projectId = intval($_SESSION['project']);
        }
        //第一个项目ID
        else
        {
            first_project:
            $this->projectId = array_keys($projects)[0];
        }

        //修改Session记录中的project
        if (!empty($_SESSION['project']) and  $this->projectId != $_SESSION['project'])
        {
            $_SESSION['project'] = $this->projectId;
        }

        $this->projectInfo = $projects[$this->projectId];
        $this->assign('_project_id', $this->projectId);
        $this->assign('_project_info', $this->projectInfo);
    }

    function isAllow($optype, $id)
    {
        if ($_SESSION['userinfo']['usertype'] == 0)
        {
            return true;
        }
        return false;
    }

    function isActiveMenu($m, $v)
    {
        if ($this->env['mvc']['controller'] == $m and $this->env['mvc']['view'] == $v)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}