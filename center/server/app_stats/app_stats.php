<?php
$time = time();
/*
 * stats统计脚本
 */
#define('DEBUG', 'on');
#define('WEBPATH', __DIR__);

require __DIR__ . '/_init.php';
#require dirname(__DIR__) . '/config.php';
#Swoole::$php->config->setPath(__DIR__.'/configs/');
function array_rebuild($array, $key, $value = '') {
	$r = array();

	foreach ($array as $k => $v) {
		$r[$v[$key]] = $value ? $v[$value] : $v;
	}

	return $r;
}

$start_time = microtime(true);

$dbData = table('st_data');
$db = $dbData->db;
$max_id = current($db->query("select max(`id`) from `st_memtemp`")->fetch());

$groupby = '`http_host`,`http_uri`,`http_app`,`http_code`,`http_json_parse`,`http_data_code`';
$select = 'count(*) as t_count,sum(http_time) as time_sum,max(http_time) as time_max,min(http_time) as time_min';

$rs = $db->query('select * from `st_uri`')->fetchall();
$uri = array();
foreach ($rs as $k => $v) {
	$uri[$v['uri']] = 1;
}

$rs = $db->query("select $groupby,$select from `st_memtemp` where `id` <= '$max_id' GROUP BY $groupby")->fetchall();

$fields = array(
	'time_sum',
	'time_max',
	'time_min',
	't_count',
);
/*	'host_id',
	'uri_id',
	'app_id',
	'type',
	'ctime'
);*/

//字符转换id
$names = array();

$puts = array();
$failed = array();

foreach ($rs as $k => $v) {
	$names["'" . $v['http_host'] . "'"] = 1;
	$names["'" . $v['http_uri'] . "'"] = 1;
	$names["'" . $v['http_app'] . "'"] = 1;
	$rs[$k]['type'] = $v['http_code'] . '_' . ($v['http_json_parse'] == 1 ? 1 : 0) . '_' . ($v['http_data_code'] == 1 ? 1 : 0);
	$names["'" . $rs[$k]['type'] . "'"] = 1;
	$key = $v['http_host'] . '/' . $v['http_uri'];
	if (!isset($uri[$key])) {
		$uri[$key] = 1;
		$db->query("insert into `st_uri` (`uri`) VALUES ('" . $db->quote($key) . "')");
	}
	if (isset($puts[$key])) {
		$puts[$key]['time_sum'] += $v['time_sum'];
		$puts[$key]['count_all'] += $v['t_count'];
		if ($v['http_code'] != 200 || $v['http_json_parse'] != 1 || $v['http_data_code'] != 1) {
			$puts[$key]['time_failed_sum'] += $v['time_sum'];
			$puts[$key]['count_failed'] += $v['t_count'];
			$puts[$key]['time_max'] = max($puts[$key]['time_max'], $v['time_max']);
			$puts[$key]['time_min'] = min($puts[$key]['time_min'], $v['time_min']);
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
			'host_id' => $v['http_host'],
			'uri_id' => $v['http_uri'],
			'app_id' => $v['http_app'],
			'time_sum' => $v['time_sum'],
			'time_max' => $v['time_max'],
			'time_min' => $v['time_min'],
			'count_all' => $v['t_count'],
			'count_failed' => 0,
			'time_failed_sum' => 0
		);
		if ($v['http_code'] != 200 || $v['http_json_parse'] != 1 || $v['http_data_code'] != 1) {
			$puts[$key]['time_failed_sum'] += $v['time_sum'];
			$puts[$key]['count_failed'] += $v['t_count'];
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
if ($names) {
	$map = array_rebuild($db->query("select * from `st_string` where `name` in (" . (implode(',', array_keys($names))) . ")")->fetchall(), 'name', 'id');
	foreach ($names as $k => $v) {
		$name = trim($k, "'");
		if (!isset($map[$name])) {
			$db->query("insert into `st_string` (`name`) VALUES (" . $k . ")");
			$map[$name] = $db->lastInsertId();
		}
	}
}

$dbFailed = table('st_failed');
foreach ($puts as $put) {
	#$put = array('ctime' => $time);
	/*foreach ($fields as $f) {
		$put['host_id'] = $map[$v['http_host']];
		$put['uri_id'] = $map[$v['http_uri']];
		$put['app_id'] = $map[$v['http_app']];
		$put['type'] = $map[$v['type']];
		if (isset($v[$f])) {
			$put[$f] = $v[$f];
		}
	}*/
	$key = $put['host_id'] . '/' . $put['uri_id'];
	$put['host_id'] = $map[$put['host_id']];
	$put['uri_id'] = $map[$put['uri_id']];
	$data_id = $dbData->put($put);
	if (isset($failed[$key])) {
		foreach ($failed[$key] as $v) {
			$v['data_id'] = $data_id;
			$dbFailed->put($v);
		}
	}
}

#$db->query("delete from `st_memtemp` where `id` <= '$max_id'");
echo "End .sptime:" . (microtime(true) - $start_time), "\n";
