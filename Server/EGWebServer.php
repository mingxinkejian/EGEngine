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
	/**
	 * WEB服务器回调
	 * @param \swoole_http_request $request
	 * @param \swoole_http_response $response
	 */
	public function onRequest(\swoole_http_request $request,\swoole_http_response $response){
		$response->header('Server', self::SERVERNAME);
		ob_start();
	
		$output=ob_get_contents();
		ob_end_clean();
		$response->end($output);
	}
	public function onClose($server, $clientId, $fromId){}
}
