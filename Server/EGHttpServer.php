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
	
		$this->_server->on('start', array($this, 'onStart'));
		$this->_server->on('request' , array( $this , 'onRequest'));
		$this->_server->on('workerStart' , array( $this , 'onWorkerStart'));
		
		if($isSetGlobal==true){
			$this->_server->setGlobal(HTTP_GLOBAL_ALL,HTTP_GLOBAL_GET | HTTP_GLOBAL_POST);
		}
	}
		
	
	public function onStart($server) {
		// TODO Auto-generated method stub
		swoole_set_process_name('EGServer');
		echo "httpServer start\n";
	}
	public function onWorkerStart(\swoole_server $server,$workerId){
		swoole_set_process_name('EGServer_worker');
		echo "WorkerStart: MasterPid={$server->master_pid}|Manager_pid={$server->manager_pid}|WorkerId={$server->worker_id}|WorkerPid={$server->worker_pid}\n";
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
