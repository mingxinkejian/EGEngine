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

	/**
	 * 魔术方法—调用DB对应的特定方法
	 * @param unknown $method
	 * @param unknown $args
	 * @return void|mixed
	 */
	public function __call($method,$args){
		//调用缓存类型自己的方法
		if(method_exists($this->_handler, $method)){
			return call_user_func_array(array($this->_handler,$method), $args);
		}else{
			return;
		}
	}
}
