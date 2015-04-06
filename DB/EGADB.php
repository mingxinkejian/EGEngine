<?php

namespace DB;

use ConfigReader\EGJson;
abstract class EGADB implements EGIDB{

	/**
	 * 配置文件
	 * @var unknown
	 */
	protected $_config;
	/**
	 * 数据库操作句柄
	 * @var unknown
	 */
	protected $_handler;
	
	
	/**
	 * 解析配置文件
	 * @param unknown $config
	 */
	public function parseConfig($configName){
	    $configData = EGJson::parse ( $configName );
	    $dbType=$configData['dbType'];
	    $this->_config=$configData[$dbType];
	}

}
