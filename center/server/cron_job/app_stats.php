<?php
$time = time();
/*
 * stats统计脚本
 */
define('DEBUG', 'on');
define('WEBPATH', __DIR__);

require dirname(__DIR__) . '/config.php';
Swoole::$php->config->setPath(__DIR__.'/configs/');

$start_time = microtime(true);

$dbTemp = table('st_memtemp');
$max_id = current($dbTemp->db->query("select max(`id`) from `st_memtemp`")->fetch());

$groupby = '`http_host`,`http_uri`,`http_code`,`http_json_parse`,`http_data_code`';
$select = 'count(*) as t_count,sum(http_time) as time_sum,max(http_time) as time_max,min(http_time) as time_min';

$rs = $dbTemp->db->query("select $groupby,$select from `st_memtemp` where `id` < '$max_id' GROUP BY $groupby")->fetchall();

$dbData = table('st_data');
$dbString = table('st_string');

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
foreach ($rs as $k => $v) {
	$names["'" . $v['http_host'] . "''"] = 1;
	$names["'" . $v['http_uri'] . "''"] = 1;
	$names["'" . $v['http_app'] . "''"] = 1;
	$rs[$k]['type'] = $v['http_code'] . '_' . ($v['http_json_parse'] == 1 ? 1 : 0) . '_' . ($v['http_data_code'] == 1 ? 1 : 0);
	$names["'" . $rs[$k]['type'] . "''"];
}

$map = array();
if ($names) {
	$rs = $dbString->db->query("select * from `st_string` where `name` in (" . (implode(',', array_keys($names))) . ")")->fetchall();
	foreach ($rs as $k => $v) {
		$map[$v['name']] = $v['id'];
	}
	foreach ($names as $k => $v) {
		$name = trim($k, "'");
		if (!isset($map[$name])) {
			$dbString->db->query("insert into `st_string` (`name`) VALUES (" . $k . ")");
			$map[$name] = $dbString->db->lastInsertId();
		}
	}
}

foreach ($rs as $v) {
	$put = array('ctime' => $time);
	foreach ($fields as $f) {
		$put['host_id'] = $map[$v['http_host']];
		$put['uri_id'] = $map[$v['http_uri']];
		$put['app_id'] = $map[$v['http_app']];
		$put['type'] = $map[$v['type']];
		if (isset($v[$f])) {
			$put[$f] = $v[$f];
		}
	}
	$dbData->put($put);
}

$dbTemp->db->query("delete from `st_memtemp` where `id` < '$max_id'");
echo "End .sptime:" . (microtime(true) - $start_time), "\n";
