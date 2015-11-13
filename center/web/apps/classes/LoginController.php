<?php
namespace App;

use Swoole\Client\CURL;

class LoginController extends \Swoole\Controller
{
    protected $uid;
    protected $userinfo;
    protected $projectId;
    protected $projectInfo;

    protected $errCode;
    protected $errMsg;

    const DEFAULT_PASSWORD = '123456';
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
            $this->uid = $_SESSION['user_id'];
            $this->userinfo = $_SESSION['userinfo'];
            if (!empty($this->userinfo['blocking']))
            {
                $this->http->finish("<h2>您的账户已被禁用！</h2><hr/><p>请联系：韩天峰/石光启/严春昊</p>");
            }
        }

        /**
         * 可参与的项目
         */
        $project_ids = $this->userinfo['project_id'];
        if ($project_ids)
        {
            $projects = table('project', 'platform')->getMap(array('in' => array('id', $project_ids)));
        }
        else
        {
            $projects = table('project', 'platform')->getMap(array('limit' => 100));
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

    /**
     * 检查是否允许
     * @param $optype
     * @param $id
     * @return bool
     */
    function isAllow($optype, $id = 0)
    {
        if ($this->userinfo['usertype'] == 0)
        {
            return true;
        }
        else
        {
            if (empty($this->userinfo['rules']))
            {
                return false;
            }
            else
            {
                return strstr($this->userinfo['rules'], $optype) !== false;
            }
        }
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

    /**
     * 同步数据到内网
     * @param $username
     * @param $update
     * @return bool
     */
    function syncIntranet($username, $update)
    {
        if (ENV_NAME != 'product')
        {
            //debug($username, $update);
            return true;
        }

        if ($username)
        {
            $update['username'] = $username;
            $api = 'user/update';
        }
        else
        {
            $api = 'user/insert';
        }

        $curl = new CURL();
        $curl->setHeader('Host', 'code.oa.com');
        $res = $curl->post('http://10.10.2.2/'.$api, $update);
        if ($res)
        {
            $json = json_decode($res, true);
            if (isset($json['code']) )
            {
                if ($json['code'] == 1)
                {
                    return true;
                }
                else
                {
                    $this->errCode = $json['code'];
                    $this->errMsg = $json['msg'];
                }
            }
            else
            {
                $this->errCode = '1001';
                $this->errMsg = 'JSON解析失败. Response='.$res;
            }
        }
        else
        {
            $this->errCode = $curl->errCode;
            $this->errMsg = $curl->errMsg;
        }
        return false;
    }

    function getGitPassword($password)
    {
        return crypt($password, '$2a$10$' . substr(md5(uniqid()), 0, 22));
    }

    function addWeiXin($params)
    {
        if (!empty($params['username']) and !empty($params['realname']) and !empty($params['weixinid']) and !empty($params['mobile']))
        {
            $token = $this->getWeixinToken();
            if ($token) {
                $data = array(
                    'userid' => $params['username'],
                    'name' => $params['realname'],
                    'department' => array(1),
                    'position' => '开发',
                    'mobile' => $params['mobile'],
                    'gender' => 1,
                    'email' => '',
                    'weixinid' => $params['weixinid'],
//    "avatar_mediaid"=> "2-G6nrLmr5EC3MNb_-zL1dDdzkd0p7cNliYu9V5w7o8K0"

                );

                $url = "https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token={$token}";
                $ch = new \Swoole\Client\CURL();
                $res = $ch->post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
                $res = json_encode($res,1);
                //'{"errcode":60102,"errmsg":"userid existed"}'
                if ($res['errcode'] == 60102) {
                    $url = "https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token={$token}";
                    $res = $this->ch->post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
                }
            }
        }
    }

    function getWeixinToken()
    {
        $key = "weixin_token";
        $token = \Swoole::$php->redis->get($key);
        if (!empty($token))
        {
            return $token;
        } else {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=wxc45d2ffe103e99c1&corpsecret=7T5TQbFHCYT5J2Z23qPKH3OaefjAIdO3FJjcap_28KUUFAbI0exS5lL4yI2fHKp1";
            $ch = new \Swoole\Client\CURL();
            $res = $ch->get($url);
            $t = json_decode($res,1);
            if (!empty($t['access_token']))
            {
                $token = $t['access_token'];
                \Swoole::$php->redis->set($key,$token,$t['expires_in']-100);
                return $token;
            }
            return false;

        }
    }
}