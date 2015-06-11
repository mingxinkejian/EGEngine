<?php

namespace Server;

abstract class EGWebSocketServer extends EGWebServer{
	
	
	public function __construct($host,$port,$logger,$isSetGlobal=true){
		if (!$host || !$port || !$logger){
			echo "please confirm the params !\n";
			exit();
		}
		$this->_logger=$logger;
		$this->_defaultHost=$host;
		$this->_defaultPort=$port;
		$this->_server=new \swoole_websocket_server($host, $port);
		if($isSetGlobal==true){
			$this->_server->setGlobal(HTTP_GLOBAL_ALL,HTTP_GLOBAL_GET | HTTP_GLOBAL_POST);
		}
	}
	
	public function onStart($server) {
		// TODO Auto-generated method stub
// 		swoole_set_process_name('EGServer');
		$this->printLog('webSocketServer start');
	}
	
	/**
	 * 当WebSocket客户端与服务器建立连接并完成握手后会回调此函数
	 * @param \swoole_websocket_server $server
	 * @param \swoole_http_request $request
	 */
	public abstract function onOpen(\swoole_websocket_server $server,\swoole_http_request $request);
	
	/**
	 * 设置onHandShake回调函数后不会再触发onOpen事件，需要应用代码自行处理
	 * 自定定握手规则，没有设置则用系统内置的（只支持version:13的）
	 * @param \swoole_http_request $request
	 * @param \swoole_http_response $response
	 * @return boolean
	 */
	public function onCustomHandShake(\swoole_http_request $request, \swoole_http_response $response){
	    if (!isset($request->header['sec-websocket-key']))
	    {
	        //'Bad protocol implementation: it is not RFC6455.'
	        $response->end();
	        return false;
	    }
	    if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $request->header['sec-websocket-key'])
	        || 16 !== strlen(base64_decode($request->header['sec-websocket-key']))
	    )
	    {
	        //Header Sec-WebSocket-Key is illegal;
	        $response->end();
	        return false;
	    }
	
	    $key = base64_encode(sha1($request->header['sec-websocket-key']
	        . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
	        true));
	    $headers = array(
	        'Upgrade'               => 'websocket',
	        'Connection'            => 'Upgrade',
	        'Sec-WebSocket-Accept'  => $key,
	        'Sec-WebSocket-Version' => '13',
	        'KeepAlive'             => 'off',
	    );
	    foreach ($headers as $key => $val)
	    {
	        $response->header($key, $val);
	    }
	    $response->status(101);
	    $response->end();
	    return true;
	}
	
	/**
	 * 接收到来自客户端的消息
	 * @param \swoole_websocket_server $server
	 * @param unknown $frame
	 */
	public abstract function onMessage(\swoole_websocket_server $server,\swoole_websocket_frame $frame);
	
	/*
	 * 关闭连接
	 */
	public function wsClose($fd,$from_id = 0){
		$this->_server->close($fd);
	}
	
	/*
	 * 封装推送消息接口
	 */
	public function wsPush($fd, $data, $binary_data = false, $finish = true){
		$this->_server->push($fd, $data,$binary_data,$finish);
	}
}
