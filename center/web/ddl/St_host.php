<?php
namespace Ddl;

use App\Form;
use App\Grid;

class St_host extends DdlModel {
	/**
	 * 表名
	 */
	public $table = 'st_host';
	/**
	 * 主键
	 */
	public $pid = 'id';
	/*
	* 数据库字段
	*/
	const  F_id = 'id', F_name = 'name';

	/**
	 * @return \Ddl\St_host
	 */
	static function getInstance($db = 'master') {
		return parent::createInstance('St_host', $db);
	}

}
