<?php
namespace App\Controller;

use Ddl\St_data_day;
use Swoole\Loader;
use Ddl\St_data;
use Ddl\St_uri;
use Swoole;
use App;

Loader::addNameSpace('Ddl', __DIR__ . '/../../ddl');

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
		if (empty($_GET['date_key'])) {
			$_GET['date_key'] = date("Y-m-d");
		}

		$host_id = !empty($_GET['h']) ? intval($_GET['h']) : 1;
		$uri_id = !empty($_GET['uri']) ? intval($_GET['uri']) : 0;
		$date = strtotime($_GET['date_key']);
		$search = isset($_GET['search']) ? $_GET['search'] : '';
		$order = isset($_GET['order']) ? $_GET['order'] : '';
		$page = empty($_GET['page']) ? 1 : max(1, intval($_GET['page']));
		$pagesize = 20;

		$mDataDay = St_data_day::getInstance('app_stats');
		$mUri = St_uri::getInstance('app_stats');

		if (!isset($_GET['order'])) {
			$_GET['order'] = 'time';
			$_GET['desc'] = 1;
		}
		$_GET['desc'] = empty($_GET['desc']) ? "" : 1;

		$uri = array_rebuild($mUri->getByHostId($host_id)->fetchall(), St_uri::F_id, St_uri::F_uri);
		if ($uri_id) {
			$uri_id = [$uri_id];
		} elseif ($search) {
			$uri_id = [];
			foreach ($uri as $k => $v) {
				if (strpos($v, $search) !== false) {
					$uri_id[] = $k;
				}
			}
		}

		$data = $mDataDay->getPageByDate($pager, $page, $pagesize, $host_id, $date, $order, $_GET['desc'], $uri_id)->fetchall();
		foreach ($data as $k => $v) {
			#$uri_ids[$v['uri_id']] = 1;
			$data[$k]['succ_rate'] = $v['count_failed'] ? round(100 - $v['count_failed'] * 100 / $v['count_all'], 2) : 100;
			$data[$k]['time_avg'] = $v['count_all'] ? round($v['time_sum'] / $v['count_all'], 2) : 0;
			$data[$k]['time_failed_avg'] = $v['count_failed'] ? round($v['time_failed_sum'] / $v['count_failed'], 2) : 0;
			#$ids[$v['type']] = 1;
			#$ids[$v['app_id']] = 1;
		}
		$pager = new Swoole\Pager([
			'total' => $pager['total'],
			'perpage' => $pager['pagesize'],
			'nowindex' => $pager['page']
		]);

		$this->assign('total', $pager->total);
		$this->assign('pager', $pager->render());
		$this->assign('data', $data);
		$this->assign('uri', $uri);
		$this->assign('uri_id', $uri_id);
		$this->display();
	}

	function detail() {
		error_reporting(E_ALL & ~E_NOTICE);
		if (empty($_GET['date_key'])) {
			$_GET['date_key'] = date("Y-m-d");
		}

		$host_id = !empty($_GET['h']) ? intval($_GET['h']) : 1;
		$uri_id = !empty($_GET['uri']) ? intval($_GET['uri']) : 0;
		$date = strtotime($_GET['date_key']);
		$search = isset($_GET['search']) ? $_GET['search'] : '';
		$order = isset($_GET['order']) ? $_GET['order'] : '';
		$page = empty($_GET['page']) ? 1 : max(1, intval($_GET['page']));
		$pagesize = 20;
		#$this->getInterfaceInfo();

		#$table = table('st_data', 'app_stats');

		$mData = St_data::getInstance('app_stats');
		$mUri = St_uri::getInstance('app_stats');

		if (!isset($_GET['order'])) {
			$_GET['order'] = 'time';
		}
		$_GET['desc'] = empty($_GET['desc']) ? "" : 1;

		#$host = array_rebuild($table->db->query("select * from `st_host`")->fetchall(), 'id', 'name');
		#$uri = array_rebuild($table->db->query("select * from `st_uri` where `host`='" . $table->db->quote($host_id) . "'")->fetchall(), 'id', 'uri');
		$uri = array_rebuild($mUri->getByHostId($host_id)->fetchall(), St_uri::F_id, St_uri::F_uri);

		/*$gets = [
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
		}*/
		if ($uri_id) {
			$uri_id = [$uri_id];
		} elseif ($search) {
			$uri_id = [];
			foreach ($uri as $k => $v) {
				if (strpos($v, $search) !== false) {
					$uri_id[] = $k;
				}
			}
			#$gets['where'][] = $search_ids ? "uri_id in (" . implode(',', $search_ids) . ")" : "1>2";
		}

		#$table->db->debug = 1;
		#$data = $table->gets($gets, $pager);
		$data = $mData->getPageByDate($pager, $page, $pagesize, $host_id, $date, $order, $_GET['desc'], $uri_id)->fetchall();
		$pager = new Swoole\Pager([
			'total' => $pager['total'],
			'perpage' => $pager['pagesize'],
			'nowindex' => $pager['page']
		]);

		$uri_ids = array();
		foreach ($data as $k => $v) {
			#$uri_ids[$v['uri_id']] = 1;
			$data[$k]['succ_rate'] = $v['count_failed'] ? round(100 - $v['count_failed'] * 100 / $v['count_all'], 2) : 100;
			$data[$k]['time_avg'] = $v['count_all'] ? round($v['time_sum'] / $v['count_all'], 2) : 0;
			$data[$k]['time_failed_avg'] = $v['count_failed'] ? round($v['time_failed_sum'] / $v['count_failed'], 2) : 0;
			#$ids[$v['type']] = 1;
			#$ids[$v['app_id']] = 1;
		}

		$this->assign('total', $pager->total);
		$this->assign('pager', $pager->render());
		$this->assign('data', $data);
		#$this->assign('host', $host);
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