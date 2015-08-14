<?php
namespace App;

/**
 * Taken from Carwheel Project.
 */
class CdnMgr {
	var $public_key = 'uclouddeveloper@eclicks.cn1433836707000728742222';
	var $private_key = '3b0f2a41dcef73acadc2e6ecb31e0a98540b0759';
	// 1: picture.eclicks.cn, 2: down.eclicks.cn, 3: down.file.chelun.com
	var $domains = array(1 => 'ucdn-casyy3', 2 => 'ucdn-z0nfeh', 3 => 'ucdn-3uuiv1');

	const F_refreshCache = 'RefreshUcdnDomainCache', F_getLog = 'GetUcdnDomainLog', F_getTraffic = 'GetUcdnDomainTraffic';
	const F_getBandwidth = 'GetUcdnDomainBandwidth';

	const REFRESHCAHE_type = 'Type', REFRESHCAHE_urlList = 'UrlList.';
	const GETLOG_beginTime = 'BeginTime', GETLOG_endTime = 'EndTime';
	const GETTRAFFIC_beginTime = 'BeginTime', GETTRAFFIC_endTime = 'EndTime', GETTRAFFIC_areacode = 'Areacode', GETTRAFFIC_UserType = 'UserType';
	const GETBANDWIDTH_beginTime = 'BeginTime', GETBANDWIDTH_endTime = 'EndTime', GETBANDWIDTH_areacode = 'Areacode', GETBANDWIDTH_daily = 'Daily';

	/**
	 *
	 * @return CdnMgr
	 */
	static function getInstance() {
		$ci = get_instance ();
		$var = __CLASS__;
		return $ci->$var;
	}

	function exec($action, $params, $domain = 1) {
		$params += array (
				'PublicKey' => $this->public_key,
				'Action' => $action,
				'DomainId' => isset($this->domains[$domain]) ? $this->domains[$domain] : $this->domains[1],
		);
		ksort ( $params );
		$url = '';
		foreach ( $params as $k => $v ) {
			$url .= $k . '=' . urlencode ( $v ) . '&';
		}
		$url .= 'Signature=' . $this->_verfy_ac ( $params );

		$r = json_decode ( file_get_contents ( "http://api.spark.ucloud.cn/?" . $url ), true );

		return $r;
	}

	private function _verfy_ac($params) {
		# 参数串排序
		$params_data = "";
		foreach ( $params as $key => $value ) {
			$params_data .= $key;
			$params_data .= $value;
		}
		$params_data .= $this->private_key;
		return sha1 ( $params_data );
		# 生成的Signature值
	}
}
