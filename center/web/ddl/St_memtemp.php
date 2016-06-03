<?php
namespace Ddl;

use App\Form;
use App\Grid;

class St_memtemp extends DdlModel {
	/**
	 * 表名
	 */
	public $table = 'st_memtemp';
	/**
	 * 主键
	 */
	public $pid = 'id';
	/*
	* 数据库字段
	*/
	const  F_id = 'id', F_client_network_type = 'client_network_type', F_client_network_sub_type = 'client_network_sub_type', F_client_network_name = 'client_network_name', F_http_method = 'http_method', F_http_code = 'http_code';
	const  F_http_time = 'http_time', F_http_body_length = 'http_body_length', F_http_post_length = 'http_post_length', F_http_data_code = 'http_data_code', F_http_header_time = 'http_header_time', F_http_total_time = 'http_total_time';
	const  F_http_json_parse = 'http_json_parse', F_http_request_time = 'http_request_time', F_addtime = 'addtime', F_http_host = 'http_host', F_http_uri = 'http_uri', F_http_app = 'http_app';
	const  F_http_ver = 'http_ver';

	/**
	 * @return \Ddl\St_memtemp
	 */
	static function getInstance($db = 'master') {
		return parent::createInstance('St_memtemp', $db);
	}

	function getByMaxId($max_id) {
		return $this->GroupBy([
			'http_host',
			'http_uri',
			'http_app',
			'http_code',
			'http_json_parse',
			'http_data_code'
		])->select([
			'count(*) as t_count',
			'sum(http_time) as time_sum',
			'max(http_time) as time_max',
			'min(http_time) as time_min',
			'http_host',
			'http_uri',
			'http_app',
			'http_code',
			'http_json_parse',
			'http_data_code'
		], 0)->getWhere([self::F_id . ' <=' => $max_id]);
	}

	function delByMaxId($max_id) {
		return $this->delWhere([self::F_id . ' <=' => $max_id]);
	}
}
