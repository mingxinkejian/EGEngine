<?php

namespace Server;

class EGWebSocketServer extends EGWebServer{
	
	
	public function __construct($host,$port,$isSetGlobal=true){
	
		$this->_defaultHost=$host;
		$this->_defaultPort=$port;
		$this->_server=new \swoole_websocket_server($host, $port);
	
		$this->_server->on('start', array($this, 'onStart'));
		$this->_server->on('message' , array( $this , 'onMessage'));
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
		echo "webSocketServer start\n";
	}
	
	public function onOpen(\swoole_websocket_server $server,$fd){
		
	}

	/**
	 * @param unknown $server
	 * @param unknown $fd
	 * @param unknown $data
	 * @param unknown $opcode OPCODE_TEXT_FRAME = 0x1 ，文本数据 OPCODE_BINARY_FRAME = 0x2 ，二进制数据
	 * @param unknown $fin
	 */
	public function onMessage($server,$fd, $data, $opcode, $fin){
		echo "receive from {$fd}:{$data},opcode:{$opcode},fin:{$fin}\n";
		$server->push($fd, "this is server");
	}
	
	public function onClose($server,$fd){
		echo "client {$fd} closed\n";
	}
}
