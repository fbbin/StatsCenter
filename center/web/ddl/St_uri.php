<?php
namespace Ddl;

use App\Form;
use App\Grid;

class St_uri extends DdlModel {
	/**
	 * 表名
	 */
	public $table = 'st_uri';
	/**
	 * 主键
	 */
	public $pid = 'id';
	/*
	* 数据库字段
	*/
	const  F_id = 'id', F_uri = 'uri', F_host = 'host';

	/**
	 * @return \Ddl\St_uri
	 */
	static function getInstance($db = 'master') {
		return parent::getInstance('St_uri', $db);
	}

	function getByHostId($host_id) {
		return $this->getWhere([self::F_host => $host_id]);
	}

	function getPageByDate() {

	}
}
