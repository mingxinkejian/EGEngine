<?php

namespace Server;

class EGWebServer extends EGBaseServer{
	const SERVERNAME ='EGEServer';
	
	protected $_webRoot;
	
	
	public function setWebRoot($webRoot){
		$this->_webRoot=$webRoot;
	}
	
	public function loadConfig($config) {
		// TODO Auto-generated method stub
		if (empty($config)){
			$config=array(
					//工作进程
					'worker_num' => 4,
					//是否守护进程
					'daemonize' => false,
					//最大请求数
					'max_request' => 10000,
					//工作模式
					'dispatch_mode' => 1
			);
		}
		$this->_config=$config;
	
		$this->_server->set($config);
	}
}
