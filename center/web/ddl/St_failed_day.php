<?php
namespace Ddl;

use App\Form;
use App\Grid;

class St_failed_day extends DdlModel {
	/**
	 * 表名
	 */
	public $table = 'st_failed_day';
	/**
	 * 主键
	 */
	public $pid = 'id';
	/*
	* 数据库字段
	*/
	const  F_id = 'id', F_http_code = 'http_code', F_json_code = 'json_code', F_data_code = 'data_code', F_data_id = 'data_id', F_t_count = 't_count';

	/**
	 * @return \Ddl\St_failed_day
	 */
	static function getInstance($db = 'master') {
		return parent::createInstance('St_failed_day', $db);
	}

	function getbyDataId($data_id) {
		return $this->getWhere([self::F_data_id => $data_id]);
	}
}
