<?php

namespace Server;

class EGWebServer extends EGBaseServer{
	const SEVERNAME ='EGEServer';
	
	protected $_webRoot;
	
	
	public function setWebRoot($webRoot){
		$this->_webRoot=$webRoot;
	}
	
	public function loadConfig($config) {
		// TODO Auto-generated method stub
		if (empty($config)){
			$config=array(
					'worker_num' => 4,
					'daemonize' => false,
					'max_request' => 10000,
					'dispatch_mode' => 1
			);
		}
		$this->_config=$config;
	
		$this->_server->set($config);
	}
}
