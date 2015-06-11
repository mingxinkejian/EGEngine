<?php

namespace Server;

class EGHttpServer extends EGWebServer{
	
	public function __construct($host,$port,$logger,$isSetGlobal=true){
	
		if (!$host || !$port || !$logger){
			echo "please confirm the params !\n";
			exit();
		}
		$this->setLogger($logger);
		
		$this->_defaultHost=$host;
		$this->_defaultPort=$port;
		$this->_server=new \swoole_http_server($host, $port);
	
		$this->addFuncCallBack('start', array($this, 'onStart'));
		$this->addFuncCallBack('request' , array( $this , 'onRequest'));
		$this->addFuncCallBack('workerStart' , array( $this , 'onWorkerStart'));
		
		if($isSetGlobal==true){
			$this->_server->setGlobal(HTTP_GLOBAL_ALL,HTTP_GLOBAL_GET | HTTP_GLOBAL_POST);
		}
	}
		
	
	public function onStart($server) {
		// TODO Auto-generated method stub
// 		swoole_set_process_name('EGServer');
		$this->printLog ( 'httpServer start' );
	}
	
	public function onRequest(\swoole_http_request $request,\swoole_http_response $response) {
		//rewrite server_software
		$response->header('Server', self::SEVERNAME);
		ob_start();
	
		$output=ob_get_contents();
		ob_end_clean();
		$response->end($output);
	
	}
}
