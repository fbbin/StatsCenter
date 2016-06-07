<?php
namespace App\Controller;

/**
 * 无访问权限限制，无需登录
 * @package App\Controller
 */
class Web extends \Swoole\Controller {

	function project_alert() {
		//取告警相关人员
		$pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
		$table = table('project_alert', 'platform');
		if ($ids = array_rebuild($table->db->query("select * from `project_alert` where `pid`='$pid'")->fetchall(), 'id', 'uid')) {
			$users = array_rebuild($table->db->query("select username,realname from user where id in (" . implode(',', $ids) . ")")->fetchall(), 'username', 'realname');
		} else {
			$users = array();
		}
		echo json_encode($users, JSON_UNESCAPED_UNICODE);
	}
}
