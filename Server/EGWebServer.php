<?php

namespace Server;
/*
 * webServer 子类为httpServer或websocketServer
 */
class EGWebServer extends EGBaseServer{
	const SERVERNAME ='EGWebServer';
	
	protected $_webRoot;
	
	public function setWebRoot($webRoot){
		$this->_webRoot=$webRoot;
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
}
