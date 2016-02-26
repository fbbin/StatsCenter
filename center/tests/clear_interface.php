<?php
define('DEBUG', 'on');
define('WEBPATH', __DIR__);

define('PUBLIC_PATH', '/data/www/public/');
define('FRAMEWORK_PATH', PUBLIC_PATH . '/framework');
$env = get_cfg_var('env.name');
$env = empty($env) ? 'product' : $env;
define('ENV_NAME', $env);
require FRAMEWORK_PATH . '/libs/lib_config.php';

if (ENV_NAME == 'local') {
    Swoole::$php->config->setPath(dirname(__DIR__) . '/server/aopnet_svr/apps/configs/local/');
} elseif (ENV_NAME == 'dev') {
    Swoole::$php->config->setPath(dirname(__DIR__) . '/server/aopnet_svr/apps/configs/dev/');
} else {
    Swoole::$php->config->setPath(dirname(__DIR__) . '/server/aopnet_svr/apps/configs/product/');
}

$get = array();
$get['order'] = 'id desc';
$get['select'] = 'DISTINCT name,module_id';
$tmp =  table("interface")->gets($get);
$interface = array();
foreach ($tmp as $t)
{
    $interface[$t['module_id']][$t['name']] = $t['name'];
}

foreach ($interface as $m_id => $face)
{
    foreach ($face as $name)
    {
        $get = array();
        $get['name'] = $name;
        $get['module_id'] = $m_id;
        $get['order'] = 'id asc';
        //\Swoole::$php->db->debug = 1;
        $res =  table("interface")->gets($get);
        $count = count($res);
        if ($count > 1) {
            //删除其他 留下最后一个
            $i = 1;
            foreach ($res as $r)
            {
                if ($i >= $count) {
                    break;
                }
                table("interface")->del($r['id']);
                echo "module_id:{$m_id} {$name} count:{$count},@{$i} del {$r['id']}\n";
                $i++;
            }
        }
    }
}





