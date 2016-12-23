<?php

namespace Server;
use Log\EGLog;

class EGSocketServer extends EGBaseServer{
	const SERVERNAME ='EGSocketServer';
	
	/**
	 * 当WebSocket客户端与服务器建立连接并完成握手后会回调此函数
	 *
	 * @param \swoole_websocket_server $server
	 * @param \swoole_http_request $request
	 */
	public function onConnect(\swoole_server $server, $fd) {
		if ($this->_debug){
			EGLog::info ( "{$fd} is connect" );
		}
	}
	
	/**
	 * 
	 * @param $fd
	 * @param $data
	 * @return boolean
	 */
	public function send($fd,$data) {
		return $this->_server->send($fd,$data);
	}
}
