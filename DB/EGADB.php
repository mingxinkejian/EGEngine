<?php

namespace DB;

abstract class EGADB implements EGIDB{

	protected $_config;
	protected $_handler;
	
	
	/**
	 * 解析配置文件
	 * @param unknown $config
	 */
	public function parseConfig($config){
		
	}
}
