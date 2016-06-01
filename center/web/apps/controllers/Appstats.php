<?php
namespace App\Controller;

use Swoole;
use App;

class Appstats extends \App\LoginController {
	//$_SESSION['userinfo']['yyuid']
	static $width = array(
		'10%',
		'10%',
		'10%',
		'10%',
		'10%',
		'10%',
		'10%',
		'30%'
	);

	static $hosts = array(
		1 => 'chelun.eclicks.cn',
		2 => 'chelun-pre.eclicks.cn'
	);

	function home() {
		$this->display();
	}

	function index() {
		error_reporting(E_ALL & ~E_NOTICE);

		$host_id = !empty($_GET['h']) ? intval($_GET['h']) : 1;
		$uri_id = !empty($_GET['uri']) ? intval($_GET['uri']) : 0;
		$date = strtotime(!empty($_GET['date_key']) ? $_GET['date_key'] : date("Y-m-d"));
		$search = isset($_GET['search']) ? $_GET['search'] : '';
		$order = isset($_GET['order']) ? $_GET['order'] : '';
		$this->getInterfaceInfo();
		$table = table('st_data', 'app_stats');
		if (!isset($_GET['order'])) {
			$_GET['order'] = 'time';
			$_GET['desc'] = 1;
		}

		$orders = [
			'time' => 'ctime',
			'count_all' => 'count_all',
			'count_fail' => 'count_failed',
			'time_max' => 'time_max',
			'time_min' => 'time_min'
		];


		$host = array_rebuild($table->db->query("select * from `st_host`")->fetchall(), 'id', 'name');
		$uri = array_rebuild($table->db->query("select * from `st_uri` where `host`='" . $table->db->quote($host_id) . "'")->fetchall(), 'id', 'uri');

		#$table->select = "`host_id`,`uri_id`,sum(time_sum) as time_sum,sum(if(`type`<>218,time_sum,0)) as fail_time_sum,sum(t_count) as t_count,sum(if(`type`<>218,t_count,0)) as faild_t_count";
		$gets = [
			#'module_id' => $_GET['module_id'],
			'order' => 'ctime desc',
			'pagesize' => 20,
			'page' => empty($_GET['page']) ? 1 : intval($_GET['page']),
		];
		if (isset($orders[$order])) {
			$gets['order'] = $orders[$order] . (empty($_GET['desc']) ? "" : " desc");
		}
		$gets['where'][] = "`host_id`='$host_id'";
		$gets['where'][] = "`ctime`>'$date' and `ctime`<='" . ($date + 86400) . "'";
		if ($uri_id) {
			$gets['where'][] = "`uri_id`='$uri_id'";
		}
		if ($search) {
			$ids = [];
			foreach ($uri as $k => $v) {
				if (strpos($v, $search) !== false) {
					$ids[] = $k;
				}
			}
			$gets['where'][] = $ids ? "uri_id in (" . implode(',', $ids) . ")" : "1>2";
		}

		$pager = null;
		#$table->db->debug = 1;
		$data = $table->gets($gets, $pager);

		$uri_ids = array();
		foreach ($data as $k => $v) {
			#$uri_ids[$v['uri_id']] = 1;
			$data[$k]['succ_rate'] = $v['count_failed'] ? round(100 - $v['count_failed'] * 100 / $v['count_all'], 2) : 100;
			$data[$k]['time_avg'] = $v['count_all'] ? round($v['time_sum'] / $v['count_all'], 5) : 0;
			$data[$k]['time_failed_avg'] = $v['count_failed'] ? round($v['time_failed_sum'] / $v['count_failed'], 5) : 0;
			#$ids[$v['type']] = 1;
			#$ids[$v['app_id']] = 1;
		}

		$this->assign('total', $pager->total);
		$this->assign('pager', $pager->render());
		$this->assign('data', $data);
		$this->assign('host', $host);
		$this->assign('uri', $uri);
		$this->assign('uri_id', $uri_id);
		$this->display();
	}

	function fail() {
		error_reporting(E_ALL & ~E_NOTICE);
		$data_id = empty($_GET['id']) ? 0 : intval($_GET['id']);

		$gets['order'] = "id";
		$gets['where'] = ["`data_id`='" . $data_id . "'"];

		$data = table('st_failed', 'app_stats')->gets($gets);
		$ret_code = [];
		foreach ($data as $d) {
			$ret_code[] = $d['http_code'] == '200'
				? ($d['json_code'] == 1
					? ["逻辑错误,data_code:" . $d['data_code'] => $d['t_count']]
					: ["JSON解析失败,json_code:" . $d['json_code'] => $d['t_count']])
				: ["服务器错误,http_code:" . $d['http_code'] => $d['t_count']];
		}

		$this->assign('ret_code', $ret_code);
		$this->display();
	}

	/**
	 * 获取接口相关信息
	 * @throws \Exception
	 */
	protected function getInterfaceInfo() {
		//\Swoole\Error::dbd();
		$gets['select'] = 'id, name';
		$gets['project_id'] = $this->projectId;

		$modules = table('module')->gets($gets);
		if (empty($_GET['date_key'])) {
			$_GET['date_key'] = date('Y-m-d');
		}

		if (empty($_GET['module_id'])) {
			$_GET['module_id'] = $modules[0]['id'];
		}

		$gets = array();
		$gets['select'] = 'id,name,alias';

		$interface_ids = $this->redis->sMembers($_GET['module_id']);
		if (!empty($interface_ids)) {
			$_ip = array();
			$_ip['in'] = array(
				'id',
				implode(',', $interface_ids)
			);
			$interfaces = table('interface')->gets($_ip);
		} else {
			$gets['module_id'] = intval($_GET['module_id']);
			$interfaces = table('interface')->gets($gets);
		}

		if (empty($_GET['interface_id'])) {
			$_GET['interface_id'] = 0;
		}
		$this->assign('interfaces', $interfaces);
		$this->assign('modules', $modules);
	}

	function history_data() {
		if (empty($_GET['module_id']) or empty($_GET['interface_id'])) {
			return $this->message(5001, "require module_id and interface_id");
		}
		$param = $_GET;

		$param['date_start'] = !empty($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d');
		$param['date_end'] = !empty($_GET['date_end']) ? $_GET['date_end'] : date('Y-m-d', time() - 86400);
		$param['date_key'] = $_GET['date_start'];

		$d1 = $this->data($param, false, false);

		$param['date_key'] = $_GET['date_end'];
		$d2 = $this->data($param, false, false);

		return json_encode(array(
			'data1' => $d1,
			'data2' => $d2
		));
	}

	function history() {
		$this->assign('width', self::$width);
		$this->display();
	}
}