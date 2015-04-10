<?php

namespace Server;

class EGHttpServer extends EGWebServer{
	
	public function __construct($host,$port,$isSetGlobal=true){
	
		$this->_defaultHost=$host;
		$this->_defaultPort=$port;
		$this->_server=new \swoole_http_server($host, $port);
	
		$this->_server->on('start', array($this, 'onStart'));
		$this->_server->on('request' , array( $this , 'onRequest'));
		if($isSetGlobal==true){
			$this->_server->setGlobal(HTTP_GLOBAL_ALL,HTTP_GLOBAL_GET | HTTP_GLOBAL_POST);
		}
	}
	
	public function loadConfig($fileName) {
		// TODO Auto-generated method stub
		$config=array();	
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
	
	
	public function onStart($server) {
		// TODO Auto-generated method stub
		echo "httpServer start\n";
	}
	
	public function onRequest(\swoole_http_request $request,\swoole_http_response $response) {
		//rewrite server_software
		$response->header('Server', self::SEVERNAME);
		ob_start();
	
		$output=ob_get_contents();
		ob_end_clean();
		if (is_null($output)){
			$response->end();
		}else{
			$response->end($output);
		}
	
	}
}
