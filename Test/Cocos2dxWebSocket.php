<?php
use Server\EGWebSocketServer;
use ConfigReader\EGJson;
use Log\EGLog;
if (version_compare ( PHP_VERSION, '5.4.0', '<' ))
	die ( 'require PHP > 5.4.0 !' );

define ( 'DS', DIRECTORY_SEPARATOR );
// 默认路径为该工程的根目录
define ( 'WEB_ROOT', __DIR__ . DS . '..' . DS );


// 为方便起见，使用autoload自动加载
require WEB_ROOT.'requireEGEngine.php';

/**
 * 此例子是用来测试websocket的，继承Server下的websocketServer即可
 * 
 */

class Cocos2dxWebSocket extends EGWebSocketServer{
	
	public function __construct($host, $port, $logger,$isSetGlobal=true){
		parent::__construct($host, $port, $logger,$isSetGlobal);
		//自定义握手协议
		$this->addFuncCallBack('handshake',array($this,'onCustomHandShake'));
		$this->addFuncCallBack('open', array($this,'onOpen'));
		$this->addFuncCallBack('start', array($this, 'onStart'));
		$this->addFuncCallBack('request' , array( $this , 'onRequest'));
		$this->addFuncCallBack('message' , array( $this , 'onMessage'));
// 		$this->addFuncCallBack('workerStart' , array( $this , 'onWorkerStart'));
		$this->addFuncCallBack('close', array( $this , 'onClose' ));
		$this->addFuncCallBack('timer',array($this,'onTimer'));
	}
	
	public function onWorkerStart(\swoole_server $server, $workerId) {
		// TODO Auto-generated method stub
		parent::onWorkerStart($server, $workerId);
	}

	public function onTimer(\swoole_websocket_server $server,$interval){
		$conn_list = $server->connection_list();
		if (!empty($conn_list)) {
			foreach($conn_list as $fd) {
				$server->push($fd, 'hello world');
			}
		}
	}
	
	public function onRequest(\swoole_http_request $request,\swoole_http_response $response){
		$response->header('Server', self::SEVERNAME);
		$response->end();
	}
	/**
	 * 接收到来自客户端的消息
	 * @param \swoole_websocket_server $server
	 * @param unknown $frame
	 */
	public function onMessage(\swoole_websocket_server $server,$frame){
		$data=json_decode($frame->data,true);
		switch ($data['type']){
			case 1:
				$server->push($frame->fd, '{"ret":1000,"data":"welcome to cocos2dx world"}');
				break;
			case 2:
				break;
		}
	}
}


$configPath = WEB_ROOT.'ServerRun'.DS. 'serverConf.json';
$configData = EGJson::parse ( $configPath );

$logger=new EGLog(WEB_ROOT);

$wsServer = new Cocos2dxWebSocket( '127.0.0.1', 9502 ,$logger,false);
$wsServer->loadConfig ( $configData ['webSocketServer'] );
$wsServer->setWebRoot ( WEB_ROOT );
$wsServer->startServer ();