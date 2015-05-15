<?php
use Server\EGWebSocketServer;
use ConfigReader\EGJson;
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

class VmstatMonitorServer extends EGWebSocketServer{
	
	public function __construct($host, $port, $logger,$isSetGlobal=true){
		parent::__construct($host, $port, $logger,$isSetGlobal);
		$this->_server->on('timer',array($this,'onTimer'));
	}
	
	public function onWorkerStart(\swoole_server $server, $workerId) {
		// TODO Auto-generated method stub
		parent::onWorkerStart($server, $workerId);
		//添加一个定时器定时监听服务器状态
		$server->addtimer(2000); //2秒
	}

	public function onTimer(\swoole_websocket_server $server,$interval){
		$conn_list = $server->connection_list();
		if (!empty($conn_list)) {
			$str = exec('vmstat',$string);
			foreach($conn_list as $fd) {
				$server->push($fd, $str);
			}
		}
	}
}


$configPath = WEB_ROOT.'ServerRun'.DS. 'serverConf.json';
$configData = EGJson::parse ( $configPath );

$wsServer = new VmstatMonitorServer( '127.0.0.1', 9502 ,null);
$wsServer->loadConfig ( $configData ['webSocketServer'] );
$wsServer->setWebRoot ( WEB_ROOT );
$wsServer->startServer ();