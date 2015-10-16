<?php
namespace App;

class LoginController extends \Swoole\Controller
{
    protected $uid;
    protected $userinfo;
    protected $projectId;
    protected $projectInfo;

    const PROJECT_ID_KEY = 'stats:web:user:project_id';

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
            else
            {
                $this->redis->set(self::PROJECT_ID_KEY.':'.$this->uid, $this->projectId);
            }
        }
        //从Session中获取
        else
        {
            $res = $this->redis->get(self::PROJECT_ID_KEY.':'.$this->uid);
            //第一个项目ID
            if (empty($res))
            {
                first_project:
                $this->projectId = array_keys($projects)[0];
                $this->redis->set(self::PROJECT_ID_KEY.':'.$this->uid, $this->projectId);
            }
            else
            {
                $this->projectId = intval($res);
            }
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

    function isActiveMenu($m, $v = '')
    {
        if ($this->env['mvc']['controller'] == $m)
        {
            if (!empty($v))
            {
                return $this->env['mvc']['view'] == $v;
            }
            return true;
        }
        else
        {
            return false;
        }
    }
}