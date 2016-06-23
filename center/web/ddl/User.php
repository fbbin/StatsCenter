<?php
namespace Ddl;

use App\Form;
use App\Grid;

class User extends DdlModel {
	/**
	 * 表名
	 */
	public $table = 'user';
	/**
	 * 主键
	 */
	public $pid = 'id';
	/*
	* 数据库字段
	*/
	const  F_id = 'id', F_uid = 'uid', F_project_id = 'project_id', F_mobile = 'mobile', F_gid = 'gid', F_usertype = 'usertype';
	const  F_username = 'username', F_password = 'password', F_realname = 'realname', F_addtime = 'addtime';

	/**
	 * @return \Ddl\User
	 */
	static function getInstance($db = 'master') {
		return parent::createInstance('User', $db);
	}

}
