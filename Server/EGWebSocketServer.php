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
	
	public function onOpen(\swoole_websocket_server $server,$fd){
		echo "client {$fd} open\n";
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
	 * @param unknown $server
	 * @param unknown $fd
	 * @param unknown $data
	 * @param unknown $opcode OPCODE_TEXT_FRAME = 0x1 ，文本数据 OPCODE_BINARY_FRAME = 0x2 ，二进制数据
	 * @param unknown $fin
	 */
	public function onMessage(\swoole_websocket_server $server,$data,$opcode, $fin){
		$connections = $server->connection_list();
	    foreach($connections as $fd)
	    {
	        $info = $server->connection_info($fd);
	        if ($fd != $data->fd and $info['websocket_status'] > 1)
	        {
	            $server->push($fd, $data->data);
	        }
	    }
	}
	
	public function onClose(\swoole_websocket_server $server, $clientId, $fromId){
		echo "client {$fromId} closed\n";
	}
}
