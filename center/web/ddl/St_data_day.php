<?php
namespace Ddl;

class St_data_day extends DdlModel {
	/**
	 * 表名
	 */
	public $table = 'st_data_day';
	/**
	 * 主键
	 */
	public $pid = 'id';
	/*
	* 数据库字段
	*/
	const F_id = 'id', F_time_sum = 'time_sum', F_time_max = 'time_max', F_time_min = 'time_min', F_time_failed_sum = 'time_failed_sum', F_count_all = 'count_all';
	const F_count_failed = 'count_failed', F_host_id = 'host_id', F_uri_id = 'uri_id', F_app_id = 'app_id', F_ctime = 'ctime', F_data_code_failed = 'data_code_failed';
	const F_succ_rate = 'succ_rate', F_time_avg = 'time_avg';

	/**
	 * @return \Ddl\St_data_day
	 */
	static function getInstance($db = 'master') {
		return parent::createInstance('St_data_day', $db);
	}

	function getByUri($host_id, $uri_id, $ctime) {
		return $this->getWhere([
			self::F_host_id => $host_id,
			self::F_uri_id => $uri_id,
			self::F_ctime => $ctime
		]);
	}

	function getPageByDate(&$pager, $page, $pagesize, $host_id, $date, $order, $desc, $uri_ids = []) {
		$where = [
			self::F_host_id => $host_id,
			self::F_ctime => $date,
		];
		if ($uri_ids) {
			$where[] = where_in(self::F_uri_id, $uri_ids);
		}
		$desc = $desc ? " desc" : "";
		$orders = [
			'time' => ['ctime' => $desc],
			'count_all' => ['count_all' => $desc],
			'count_fail' => ['count_failed' => $desc],
			'time_max' => ['time_max' => $desc],
			'time_min' => ['time_min' => $desc],
			'succ_rate' => ['succ_rate' => $desc],
			'time_avg' => ['time_avg' => $desc]
		];
		if (isset($orders[$order])) {
			$this->orderBy($orders[$order]);
		}
		return $this->getPageWhere($where, $pager, $page, $pagesize);
	}

}
