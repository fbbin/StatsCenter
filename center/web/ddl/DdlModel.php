<?php
namespace Ddl;

class DdlModel {
	/**
	 * 表名
	 */
	public $table;
	/**
	 * @var string 主键名
	 */
	public $pid = 'id';
	/**
	 * @var bool 调试模式，只打印sql不执行
	 */
	public $debug = false, $last_sql = '';
	public $last_errno = 0, $last_err_string = '';

	/**
	 * @var \Swoole\Model 数据table实例
	 */
	private $m;
	private $ar_order_by = [], $ar_select = [], $ar_group_by = [];
	private static $where = [];

	/**
	 * @var array 所有的ddl单例数组
	 */
	private static $_ddl = [];

	static function getInstance($table_name, $db = 'master') {
		if (!isset(self::$_ddl[$table_name])) {
			$class_name = "\\Ddl\\{$table_name}";
			$model = new $class_name($db);
			self::$_ddl[$table_name] = $model;
		}
		return self::$_ddl[$table_name];
	}

	function __construct($db = 'master') {
		$this->m = table($this->table, $db);
		$this->last_errno = $this->m->db->errno();
		$this->last_err_string = $this->m->db->_db->error;
	}

	/**
	 * 写入一条记录
	 * @param $data
	 * @return int 返回insert id
	 */
	function insert($data) {
		$field = "";
		$values = "";
		foreach ($data as $key => $value) {
			$value = $this->quote($value);
			$field = $field . "`$key`,";
			$values = $values . "'$value',";
		}
		$field = substr($field, 0, -1);
		$values = substr($values, 0, -1);
		$sql = "INSERT INTO {$this->table} ($field) VALUES ($values)";
		return $this->query($sql) ? $this->m->db->lastInsertId() : false;
	}

	function insertBat($data) {
		$field = "";
		$values = [];
		foreach ($data as $lid => $line) {
			$values[$lid] = '';
			foreach ($line as $key => $value) {
				if ($lid == 0) {
					$field = $field . "`$key`,";
				}
				$value = $this->quote($value);
				$values[$lid] = $values[$lid] . "'$value',";
			}
			$values[$lid] = '(' . substr($values[$lid], 0, -1) . ')';
		}
		$field = substr($field, 0, -1);
		$values = implode(',', $values);
		$sql = "INSERT INTO {$this->table} ($field) VALUES $values";
		return $this->query($sql) ? true : false;
	}

	/**
	 * 根据主键删除一条记录
	 * @param $pId
	 * @param $where
	 * @return bool
	 */
	function del($pId, $where = []) {
		$w = [
			$this->pid => $pId
		];
		foreach ($where as $k => $v) {
			if (is_numeric($k)) {
				$w[] = $v;
			} elseif (!isset($w[$k])) {
				$w[$k] = $v;
			}
		}
		return $this->delWhere($w);
	}

	function delByIds($pIds, $where = []) {
		if (!$this->pid) {
			return false;
		}
		$w = [where_in($this->pid, $pIds)];
		foreach ($where as $k => $v) {
			if (is_numeric($k)) {
				$w[] = $v;
			} elseif (!isset($w[$k])) {
				$w[$k] = $v;
			}
		}
		return $this->delWhere($w);
	}

	function getById($pId) {
		return $this->getWhere([
			$this->pid => $pId
		]);
	}

	function getByIds($pIds) {
		return $this->getWhere(where_in(
			$this->pid,
			$pIds
		));
	}

	/**
	 * 根据主键更新一条记录
	 * @param $pid_or_pids
	 * @param $set
	 * @return bool
	 */
	function update($pid_or_pids, $set) {
		if (!$this->pid || !$set) {
			return false;
		}
		return $this->updateWhere(is_array($pid_or_pids) ? [$this->whereIn($this->pid, $pid_or_pids)] : [
			$this->pid => $pid_or_pids
		], $set);
	}

	function getPage(&$pager, $page = 1, $pagesize = 15) {
		return $this->getPageWhere([], $pager, $page, $pagesize);
	}

	/**
	 * 根据条件返回查询sql
	 * @param array $where
	 * @param int $limit
	 * @param int $offset
	 * @return \Swoole\Database\MySQLiRecord
	 */
	function getSql($where, $limit = null, $offset = null) {
		$this->whereInit($where);
		if ($offset !== null) {
			$_limit = "$offset,$limit";
		} else {
			$_limit = ($limit === null ? "" : $limit);
		}
		$order = '';
		$select = '*';
		$groupby = '';
		if ($_limit) {
			$_limit = " LIMIT $_limit";
		}
		if ($this->ar_group_by) {
			$groupby = " GROUP BY " . implode(',', $this->ar_group_by);
		}
		if ($this->ar_order_by) {
			$order = '';
			foreach ($this->ar_order_by as $k => $v) {
				concat($order, "$k $v");
			}
			$order = " ORDER BY $order";
			$this->ar_order_by = [];
		}
		if ($this->ar_select) {
			$select = implode(',', $this->ar_select);
			$this->ar_select = [];
		}
		self::$where = [];
		return "SELECT $select FROM `{$this->table}`$where$groupby$order$_limit";
	}

	/**
	 * 取全部
	 * @param int $limit
	 * @param int $offset
	 * @return \Swoole\Database\MySQLiRecord
	 */
	function get($limit = null, $offset = null) {
		return $this->getWhere(array(), $limit, $offset);
	}

	/**
	 * 过滤
	 * @param $value
	 * @return mixed
	 */
	function quote($value) {
		return $this->m->db->quote($value);
	}

	function getConst($prefix = 'F') {
		static $const;
		if (!isset($const)) {
			$class = new \ReflectionClass($this);
			$const = array(0 => array_flip($class->getConstants()));
		}
		if (!$prefix) {
			return $const[0];
		}
		if (!isset($const[$prefix])) {
			$prefix = $prefix . '_';
			$len = strlen($prefix);
			$const[$prefix] = array();
			foreach ($const as $k => $v) {
				if (substr($v, 0, $len) == $prefix) {
					$const[$prefix][$k] = $v;
				}
			}
		}
		return $const[$prefix];
	}


	function errorDisplay($display) {
		\Swoole\Error::$display = $display;
	}

	/**
	 * 格式化字段名和值为sql格式
	 * @param $where
	 * @param $op
	 * @param bool $brackets
	 * @return string
	 */
	static function whereFormat($where, $op, $brackets = false) {
		$r = '';
		foreach ($where as $k => $v) {
			if (is_numeric($k)) {
				concat($r, $v, $op);
				continue;
			}
			$ar = explode(' ', $k);
			$k = '';
			$end = '=';
			foreach ($ar as $key) {
				if ($key) {
					if ($key != 'like' && strpos('+-*/^=<>%!~', $key[0]) === false) {
						concat($k, "`$key`", ' ');
					} else {
						concat($k, "$key", ' ');
						$end = '';
					}
				}
			}
			$v = " '" . addslashes($v) . "'";
			concat($r, "$k$end$v", $op);
		}
		$w = $brackets ? "($r)" : $r;
		self::$where[$w] = 1;
		return $w;
	}

	static function whereIn($key, $values) {
		if (!$values) {
			return "FALSE";
		}
		if (!is_array($values) || !$values) {
			$values = [$values];
		}
		$in = '';
		foreach ($values as $k => $v) {
			concat($in, "'" . addslashes($v) . "'");
		}
		$w = "`$key` IN ($in)";
		self::$where[$w] = 1;
		return $w;
	}


	/**
	 * 根据条件删除记录
	 * @param $where
	 * @return db_debug|\Swoole\Database\MySQLiRecord
	 */
	protected function delWhere($where) {
		$this->whereInit($where);
		$sql = "DELETE FROM {$this->table}$where";
		return $this->query($sql);
	}

	/**
	 * 根据条件更新记录
	 * @param $where
	 * @param $set
	 * @return bool
	 */
	protected function updateWhere($where, $set) {
		$this->whereInit($where);
		if (is_array($set)) {
			$set = self::whereFormat($set, ',');
		}
		$sql = "UPDATE {$this->table} SET $set$where";
		return $this->query($sql) ? $this->m->db->getAffectedRows() : false;
	}

	/**
	 * 执行sql
	 * @param $sql
	 * @return \Swoole\Database\MySQLiRecord
	 */
	protected function query($sql) {
		$this->last_sql = $sql;
		if ($this->debug) {
			echo $sql, "<br>";
		} else {
			$r = $this->m->db->query($sql);
			$this->last_errno = $this->m->db->errno();
			#$this->last_err_string = $this->m->db->_db->error;
			return $r;
		}
		return new db_debug();
	}

	/**
	 * 指定排序
	 * @param $f
	 * @param string $desc
	 * @return $this
	 */
	protected function orderBy($f, $desc = '', $escape = true) {
		$this->ar_order_by[$escape ? "`$f`" : $f] = strtolower(trim($desc)) == 'desc' ? 'desc' : 'asc';
		return $this;
	}

	protected function GroupBy($f, $desc = '', $escape = true) {
		if (is_array($f)) {
			foreach ($f as $v) {
				$this->ar_group_by[] = $escape ? "`$v`" : $v;
			}
		} else {
			$this->ar_group_by[] = $escape ? "`$f`" : $f;
		}
		return $this;
	}

	/**
	 * 指定select项
	 * @param $sel
	 * @return $this
	 */
	public function select($sel, $escape = true) {
		if ($sel == '*') {
			$this->ar_select = array();
		} elseif (is_array($sel)) {
			foreach ($sel as $v) {
				$this->ar_select[] = $escape ? "`$v`" : $v;
			}
		} else {
			$this->ar_select[] = $escape ? "`$sel`" : $sel;
		}
		return $this;
	}


	/**
	 * 按条件取
	 * @param array $where
	 * @param int $limit
	 * @param int $offset
	 * @return \Swoole\Database\MySQLiRecord
	 */
	protected function getWhere($where, $limit = null, $offset = null) {
		$sql = $this->getSql($where, $limit, $offset);
		return $this->query($sql);
	}

	/**
	 * 按翻页取
	 * @param array $where
	 * @param int $page
	 * @param int $pagesize
	 * @return \Swoole\Database\MySQLiRecord
	 */
	protected function getPageWhere($where, &$pager, $page = 1, $pagesize = 15) {
		$page = str2int($page, 1, 1);
		$pager = [
			'total' => $this->getCount($where),
			'pagesize' => $pagesize,
			'page' => $page
		];
		$sql = $this->getSql($where, $pagesize, ($page - 1) * $pagesize);
		return $this->query($sql);
	}


	/**
	 * 根据条件取记录数
	 * @param $where
	 * @return int
	 */
	protected function getCount($where) {
		$this->whereInit($where);
		$sql = "SELECT count(*) as total FROM {$this->table}$where";
		$r = $this->query($sql)->fetch();
		return $r ? $r['total'] : 0;
	}

	/**
	 * 初始化where数组为sql字符串
	 * @param $where
	 */
	private function whereInit(&$where) {
		if (is_array($where)) {
			$where = where_and($where);
		} else {
			if ($where !== 'FALSE' && !isset(self::$where[$where])) {
				trigger_error("在代码中直接写SQL语句有风险", E_USER_WARNING);
			}
		}
		if ($where) {
			$where = " WHERE $where";
		}
	}
}

function where_in($key, $values) {
	return DdlModel::whereIn($key, $values);
}

/**
 * 生成where串
 * @param $where
 * @param bool $brackets
 * @return string
 */
function where_and($where, $brackets = false) {
	return DdlModel::whereFormat($where, ' and ', $brackets);
}

/**
 * 生成where串
 * @param $where
 * @param bool $brackets
 * @return string
 */
function where_or($where, $brackets = false) {
	return DdlModel::whereFormat($where, ' or ', $brackets);
}

/**
 * 连接两个字符串
 * @param $str1
 * @param $str2
 * @param string $op
 */
function concat(&$str1, $str2, $op = ',') {
	if ($str1) {
		$str1 .= "$op$str2";
	} else {
		$str1 = $str2;
	}
}

/**
 * 兼容debug模式时不出错用
 * @package Ddl
 */
class db_debug {
	/**
	 * @param $name
	 * @param $arguments
	 */
	function __call($name, $arguments) {
		return;
	}

	/**
	 * @param $name
	 */
	function __get($name) {
		return;
	}

	function fetch() {
		return [];
	}

	function fetchAll() {
		return [];
	}
}