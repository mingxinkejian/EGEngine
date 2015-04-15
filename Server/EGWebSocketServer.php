<?php

namespace Server;

class EGWebSocketServer extends EGWebServer{
	
	
	public function __construct($host,$port,$isSetGlobal=true){
	
		$this->_defaultHost=$host;
		$this->_defaultPort=$port;
		$this->_server=new \swoole_websocket_server($host, $port);
	
		$this->_server->on('start', array($this, 'onStart'));
		$this->_server->on('request' , array( $this , 'onRequest'));
		$this->_server->on('message' , array( $this , 'onMessage'));
		
		if($isSetGlobal==true){
			$this->_server->setGlobal(HTTP_GLOBAL_ALL,HTTP_GLOBAL_GET | HTTP_GLOBAL_POST);
		}
	}
	
	public function onStart($server) {
		// TODO Auto-generated method stub
		echo "webSocketServer start\n";
	}
	
	public function onOpen(\swoole_websocket_server $server,\swoole_http_request $request){
		echo "client {$request->fd} open\n";
	}
	
	public function onRequest(\swoole_http_request $request,\swoole_http_response $response){
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


	/**
	 * 接收到来自客户端的消息
	 * @param \swoole_websocket_server $server
	 * @param unknown $frame
	 */
	public function onMessage(\swoole_websocket_server $server,$frame){
		echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
	}
	
	public function onClose(\swoole_websocket_server $server, $fromId){
		echo "client {$fromId} closed\n";
	}
}
