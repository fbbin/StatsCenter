<?php
$time = time() - 300;
require __DIR__ . '/_init.php';

Swoole\Loader::addNameSpace('Ddl', __DIR__ . '/../../web/ddl');

function array_rebuild($array, $key, $value = '') {
	$r = array();

	foreach ($array as $k => $v) {
		$r[$v[$key]] = $value ? $v[$value] : $v;
	}

	return $r;
}

$mMemtemp = \Ddl\St_memtemp::getInstance();
$mData = \Ddl\St_data::getInstance();
$mDataDay = \Ddl\St_data_day::getInstance();
$mHost = \Ddl\St_host::getInstance();
$mUri = \Ddl\St_uri::getInstance();
$mFailed = \Ddl\St_failed::getInstance();
$mFailedDay = \Ddl\St_failed_day::getInstance();

$start_time = microtime(true);
$max_id = current($mMemtemp->select('max(`id`)', 0)->get()->fetch());
if (!$max_id) {
	echo "no data\n";
	exit;
}

$host = array_rebuild($mHost->get()->fetchall(), \Ddl\St_host::F_name, \Ddl\St_host::F_id);

$rs = $mUri->get()->fetchall();
$uri = array();
foreach ($rs as $k => $v) {
	$uri[$v['host'] . '/' . $v['uri']] = $v['id'];
}

$rs = $mMemtemp->getByMaxId($max_id)->fetchall();

//字符转换id
$puts = array();
$failed = array();

foreach ($rs as $k => $v) {
	if (!isset($host[$v['http_host']])) {
		$host[$v[\Ddl\St_memtemp::F_http_host]] = $mHost->insert([\Ddl\St_host::F_name => $v[\Ddl\St_memtemp::F_http_host]]);
	}
	#$rs[$k]['type'] = $v['http_code'] . '_' . ($v['http_json_parse'] == 1 ? 1 : 0) . '_' . ($v['http_data_code'] == 1 ? 1 : 0);
	$key = $host[$v['http_host']] . '/' . $v['http_uri'];
	if (!isset($uri[$key])) {
		$uri[$key] = $mUri->insert([
			\Ddl\St_uri::F_host => $host[$v[\Ddl\St_memtemp::F_http_host]],
			\Ddl\St_uri::F_uri => $v[\Ddl\St_memtemp::F_http_uri]
		]);
	}

	if (isset($puts[$key])) {
		$puts[$key]['time_sum'] += $v['time_sum'];
		$puts[$key]['count_all'] += $v['t_count'];
		$puts[$key]['time_max'] = max($puts[$key]['time_max'], $v['time_max']);
		$puts[$key]['time_min'] = min($puts[$key]['time_min'], $v['time_min']);
		if ($v['http_code'] != 200 || $v['http_json_parse'] != 1 || $v['http_data_code'] != 1) {
			if ($v['http_code'] != 200 || $v['http_json_parse'] != 1) {
				$puts[$key]['time_failed_sum'] += $v['time_sum'];
				$puts[$key]['count_failed'] += $v['t_count'];
			} else {
				$puts[$key]['data_code_failed'] = 1;
			}
			$failed[$key][] = array(
				'http_code' => $v['http_code'],
				'json_code' => $v['http_json_parse'],
				'data_code' => $v['http_data_code'],
				't_count' => $v['t_count']
			);
		}
	} else {
		$puts[$key] = array(
			'ctime' => $time,
			'host_id' => $host[$v['http_host']],
			'uri_id' => $uri[$key],
			'time_sum' => $v['time_sum'],
			'time_max' => $v['time_max'],
			'time_min' => $v['time_min'],
			'count_all' => $v['t_count'],
			'count_failed' => 0,
			'time_failed_sum' => 0
		);
		if ($v['http_code'] != 200 || $v['http_json_parse'] != 1 || $v['http_data_code'] != 1) {
			if ($v['http_code'] != 200 || $v['http_json_parse'] != 1) {
				$puts[$key]['time_failed_sum'] += $v['time_sum'];
				$puts[$key]['count_failed'] += $v['t_count'];
			} else {
				$puts[$key]['data_code_failed'] = 1;
			}
			$failed[$key][] = array(
				'http_code' => $v['http_code'],
				'json_code' => $v['http_json_parse'],
				'data_code' => $v['http_data_code'],
				't_count' => $v['t_count']
			);
		}
	}
}

$map = array();
$today = strtotime(date("Y-m-d", $time));
foreach ($puts as $key => $put) {
	$put['succ_rate'] = $put['count_all'] ? 100 - ceil($put['count_failed'] * 10000 / $put['count_all']) / 100 : 100;
	$put['time_avg'] = $put['count_all'] ? ($put['time_sum'] - $put['time_failed_sum']) / ($put['count_all'] - $put['count_failed']) : 0;
	//写全天
	$rs = $mDataDay->getByUri($put['host_id'], $put['uri_id'], $today)->fetch();
	if ($rs) {
		$count_failed = $rs['count_failed'] + $put['count_failed'];
		$count_all = $rs['count_all'] + $put['count_all'];
		$time_sum = $rs['time_sum'] + $put['time_sum'];
		$time_failed_sum = $rs['time_failed_sum'] + $put['time_failed_sum'];

		$data = [
			\Ddl\St_data_day::F_count_all . ' = ' . \Ddl\St_data_day::F_count_all . ' + ' => $put[\Ddl\St_data::F_count_all],
			\Ddl\St_data_day::F_count_failed . ' = ' . \Ddl\St_data_day::F_count_failed . ' + ' => $put[\Ddl\St_data::F_count_failed],
			\Ddl\St_data_day::F_time_failed_sum . ' = ' . \Ddl\St_data_day::F_time_failed_sum . ' + ' => $put[\Ddl\St_data::F_time_failed_sum],
			\Ddl\St_data_day::F_time_max => max($put[\Ddl\St_data::F_time_max], $rs[\Ddl\St_data_day::F_time_max]),
			\Ddl\St_data_day::F_time_min => min($put[\Ddl\St_data::F_time_min], $rs[\Ddl\St_data_day::F_time_min]),
			\Ddl\St_data_day::F_time_sum . ' = ' . \Ddl\St_data_day::F_time_sum . ' + ' => $put[\Ddl\St_data::F_time_sum],
			\Ddl\St_data_day::F_data_code_failed => max($put[\Ddl\St_data::F_data_code_failed], $rs[\Ddl\St_data_day::F_data_code_failed]),
			\Ddl\St_data_day::F_succ_rate => $count_all ? 100 - ceil($count_failed * 10000 / $count_all) / 100 : 100,
			\Ddl\St_data_day::F_time_avg => $count_all ? ($time_sum - $time_failed_sum) / ($count_all - $count_failed) : 0,
		];
		$mDataDay->update($rs[\Ddl\St_data_day::F_id], $data);
		$data_day_id = $rs[\Ddl\St_data_day::F_id];
	} else {
		$data = $put;
		$data[\Ddl\St_data_day::F_ctime] = $today;
		$data_day_id = $mDataDay->insert($data);
	}
	//写分时
	$data_id = $mData->insert($put);
	//错误列表
	$rs = $mFailedDay->getbyDataId($data_day_id)->fetchall();
	$fs = [];
	foreach ($rs as $k => $v) {
		$fs[$v[\Ddl\St_failed_day::F_http_code] . '_' . $v[\Ddl\St_failed_day::F_json_code] . '_' . $v[\Ddl\St_failed_day::F_data_code]] = $v;
	}
	if (isset($failed[$key])) {
		foreach ($failed[$key] as $v) {
			//全天
			if (isset($fs[$v[\Ddl\St_failed::F_http_code] . '_' . $v[\Ddl\St_failed::F_json_code] . '_' . $v[\Ddl\St_failed::F_data_code]])) {
				$mFailedDay->update($fs[$v[\Ddl\St_failed::F_http_code] . '_' . $v[\Ddl\St_failed::F_json_code] . '_' . $v[\Ddl\St_failed::F_data_code]][\Ddl\St_failed_day::F_id], [
					\Ddl\St_failed_day::F_t_count . ' = ' . \Ddl\St_failed_day::F_t_count . ' + ' => $v[\Ddl\St_failed::F_t_count]
				]);
			} else {
				$data = $v;
				$data['data_id'] = $data_day_id;
				$mFailedDay->insert($data);
			}
			//分时
			$v['data_id'] = $data_id;
			$mFailed->insert($v);
		}
	}
}

$mMemtemp->delByMaxId($max_id);
echo "End .sptime:" . (microtime(true) - $start_time), "\n";
