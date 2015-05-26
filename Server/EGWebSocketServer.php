<?php

namespace Server;

class EGWebSocketServer extends EGWebServer{
	
	
	public function __construct($host,$port,$logger,$isSetGlobal=true){
		if (!$host || !$port || !$logger){
			echo "please confirm the params !\n";
			exit();
		}
		$this->setLogger($logger);
		
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
	
	public function onWorkerStart(\swoole_server $server,$workerId){
// 		swoole_set_process_name('EGServer_worker');
		$this->printLog( "WorkerStart: MasterPid={$server->master_pid}|Manager_pid={$server->manager_pid}|WorkerId={$server->worker_id}|WorkerPid={$server->worker_pid}");
	}

	public function onOpen(\swoole_websocket_server $server,\swoole_http_request $request){
		$this->printLog(  "client {$request->fd} open");
	}
	
	public function onRequest(\swoole_http_request $request,\swoole_http_response $response){
		$response->header('Server', self::SEVERNAME);
		ob_start();
		
		$output=ob_get_contents();
		ob_end_clean();
		$response->end($output);
		
	}
	/**
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
	public function onMessage(\swoole_websocket_server $server,$frame){
// 		echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
	}
	
}
