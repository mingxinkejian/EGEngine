<?php

namespace GateWayWorker;
use Server\EGTcpServer;
use Extension\EGSockBuffer;
use Log\EGLog;
/**
 * 
 * |Do the most simple game's server framework
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |author mintingjian Date:2016-12-19 Time:下午6:25:36
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |	email:mingtingjian@sina.com                          
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Copyright (c) 2015 EasyGameEngine
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Desc:
 * 注册中心，用于注册Gateway和worker
 */
class EGGateWayRegister extends EGTcpServer{
	const SERVERNAME ='EGGateWayRegister';
	public $_secretKey = '';
	//最大包长
	const MAX_PACKAGE_LEN  = 8000000;
	protected $_buffer = array();
	protected $_bufferLen = array();
	
	//用两个SwooleTable来处理
	public $_gatewayTable;
	public $_workerTable;
	public $_timerTable;
	
	protected $_serverStartTime = 0;
	
	public function __construct($host, $port) {
		parent::__construct($host, $port);
		//创建保存网关和服务器连接的持久化
		$this->createConnectionsTable();
		
		$this->_server->on('connect',array($this,'onConnect'));
		$this->_server->on('receive',array($this,'onReceive'));
		$this->_server->on('close',array($this,'onClose'));
		$this->_serverStartTime = time();
	}
	
	protected function createConnectionsTable() {
		//此处也可以使用redis等缓存处理
		//最大可以支持8192台网关
		$this->_gatewayTable = new \swoole_table(8192);
		$this->_gatewayTable->column("fd", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("remoteIp", \swoole_table::TYPE_STRING,2048);
		$this->_gatewayTable->column("remotePort", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("connectTime", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("isAuth", \swoole_table::TYPE_INT);
		$this->_gatewayTable->create();
		//最大可以支持8192台服务器
		$this->_workerTable = new \swoole_table(8192);
		$this->_gatewayTable->column("fd", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("remoteIp", \swoole_table::TYPE_STRING,2048);
		$this->_gatewayTable->column("remotePort", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("connectTime", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("isAuth", \swoole_table::TYPE_INT);
		$this->_workerTable->create();
		
		//用来保存定时器的timerId
		$this->_timerTable = new \swoole_table(8192);
		$this->_timerTable->column("timerId", \swoole_table::TYPE_INT);
		$this->_timerTable->create();
	}
	
	
	public function onConnect($server,$fd,$fromId) {
		//连接需要进行验证，没有通过验证的连接定时删除，默认设定为10秒
		$tickTimerId = $server->after(10000, function() use ($server, $fd) {
			$connectInfo = $server->connection_info($fd);
			$this->serverClose($fd);
			EGLog::warn('Register auth timeout : ',json_encode($connectInfo));
			//删除定时器
			$this->removeTimer($fd);
		});
		$this->_timerTable->set($fd, array("timerId"=>$tickTimerId));
	}
	/**
	 * 收到消息后处理
	 * @see \Server\EGBaseServer::onReceive()
	 */
	public function onReceive($server,$fd,$fromId,$data) {
		if ($this->onReceiveUnpack($server,$fd,$fromId,$data) == false) {
			return;	
		}
		//拆包
		$packData = new EGSockBuffer();
		$packData->setData($this->_buffer[$fd]);
		$jsonData = @json_decode($packData->readString());
		//收到发送的包后进行验证，验证通过的将定时器剔除
		if (empty($jsonData['event'])) {
			$connectInfo = $server->connection_info($fd);
			$error = "Bad request for Register service. Request info(".json_encode($connectInfo)." , Request Buffer:".json_encode($jsonData).")";
            EGLog::error($error);
            $this->serverClose($fd);
            
            $this->removeTimer($fd);
            return;
		}
		//事件
		$event = $jsonData['event'];
		//秘钥
		$secretKey = isset($data['secretKey']) ? $jsonData['secretKey'] : '';
		
		switch ($event) {
			//gateway连接
			case 'gatewayConnect':
				{
					if (empty($data['address'])) {
						EGLog::warn('address not found');
						$this->removeTimer($fd);
						$this->serverClose($fd);
						return;
					}
					
					if ($secretKey != $this->_secretKey) {
						EGLog::warn("Register: Key does not match {$secretKey} !== {$this->_secretKey}");
						$this->removeTimer($fd);
						$this->serverClose($fd);
						return;
					}
					//保存当前连接
					$this->setConnectionsToTable($this->_gatewayTable, $fd);
					//判断地址存不存在，不存在的话关闭该连接
					$this->broadcastAddresses();
				}
				break;
			//worker连接
			case 'workerServerConnect':
				{
					
					if ($secretKey != $this->_secretKey) {
						EGLog::warn("Register: Key does not match $secretKey !== {$this->_secretKey}");
						$this->removeTimer($fd);
						$this->serverClose($fd);
						return;
					}
					//保存当前连接
					$this->setConnectionsToTable($this->_gatewayTable, $fd);
					$this->broadcastAddresses($fd);
				}
				break;
			case 'ping':
				break;
			default:
				{
					$connectInfo = $server->connection_info($fd);
					$error = "Register unknown event:$event Info: ".json_encode($connectInfo)." , Request Buffer:".json_encode($jsonData).")";
					EGLog::error($error);
					$this->serverClose($fd);
				}
				break;
		}
	}
	/**
	 * 解包
	 * @param unknown $server
	 * @param unknown $fd
	 * @param unknown $fromId
	 * @param unknown $data
	 * @return boolean
	 */
	private function onReceiveUnpack($server,$fd,$fromId,$data) {
		
		if (empty($this->_buffer[$fd])) {
			$this->_buffer[$fd] = '';
			$this->_bufferLen[$fd] = 0;
		}
		
		$this->_buffer[$fd] .= $data;
		$this->_bufferLen[$fd] = strlen($this->_buffer[$fd]);
		
		$buffer = &$this->_buffer[$fd];
		
		do{
			if ($this->_bufferLen[$fd] == 0) {
				$nLen = unpack('Nlen', substr($buffer, 0,4));
				$this->_bufferLen[$fd] = $nLen['len'];
				if ($nLen > self::MAX_PACKAGE_LEN) {
					$this->removeTimer($fd);
					$this->serverClose($fd);
					return false;
				}
			}
			
			if(strlen($buffer) >= $this->_bufferLen[$fd]) {
				//package 为完整的包
				$package = substr($buffer, 0, $this->_bufferLen[$fd]);
				$buffer = substr($buffer, $this->_bufferLen[$fd]);
				$this->_bufferLen[$fd] = 0;
			}else {
				break;
			}
			
			
		}while(strlen($buffer) > 0);
		
		return true;
	}
	
	public function onClose($server,$fd,$fromId) {
		//将连接从table中剔除
		if ($this->_gatewayTable->exist($fd)) {
			$this->_gatewayTable->del($fd);
			$this->broadcastAddresses();
		}
		
		if($this->_workerTable->exist($fd)) {
			$this->_workerTable->del($fd);
		}
		
	}
	
	/**
	 * 向woker服务器广播gateway内部通讯地址
	 * @param string $connection
	 */
	public function broadcastAddresses($fd = NULL) {
		$gatewayArray = array();
		foreach($this->_workerTable as $row)
		{
			array_push($gatewayArray, $row);
		}
		//读取网关连接向后端服务器发送广播
		
		$data = array(
				'event' => 'broadcastAddresses',
				'addresses' => $gatewayArray
		);
		
		$jsonData = json_encode($data);
		if ($fd) {
			$this->_server->send($fd,$jsonData);
			return;
		}
		
		//遍历后端服务器广播gateway内部通讯地址
		foreach($this->_workerTable as $row)
		{
			$fd = $row['fd'];
			$this->send($fd, $jsonData);
		}
		
	}
	
	/**
	 * 删除定时器
	 * @param unknown $fd
	 */
	private function removeTimer($fd){
		if ($this->_timerTable->exist($fd)) {
			$tickTimerId = $this->_timerTable->exist($fd);
			 
			swoole_timer_clear($tickTimerId['timerId']);
			$this->_timerTable->del($fd);
		}
	}
	
	/**
	 * 将连接保存到table里
	 * @param \swoole_table $table
	 * @param unknown $fd
	 */
	private function setConnectionsToTable(\swoole_table $table,$fd) {
		$this->_gatewayTable->column("fd", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("remoteIp", \swoole_table::TYPE_STRING,2048);
		$this->_gatewayTable->column("remotePort", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("connectTime", \swoole_table::TYPE_INT);
		$this->_gatewayTable->column("isAuth", \swoole_table::TYPE_INT);
		$connectInfo = $this->_server->connection_info($fd);
		
		$table->set($fd,
				array(
				'fd'=>$fd,
				'remoteIp'=>$connectInfo['remote_ip'],
				'remotePort'=>$connectInfo['remote_port'],
				'connectTime'=>$connectInfo['connect_time'],
				'isAuth'=>1
				)
		);
	}
}
