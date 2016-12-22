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
	public $_secretKey;
	
	//用两个SwooleTable来处理
	public $_gatewayTable;
	public $_workerTable;
	
	public function __construct($host, $port) {
		parent::__construct($host, $port);
		
		//最大可以支持8192台网关
		$this->_gatewayTable = new \swoole_table(8192);
		//最大可以支持8192台网关
		$this->_workerTable = new \swoole_table(8192);
		
		$this->_server->on('connect',array($this,'onConnect'));
		$this->_server->on('receive',array($this,'onReceive'));
		$this->_server->on('close',array($this,'onClose'));
	}
	
	public function onConnect($server,$fd,$fromId) {
	
	}
	/**
	 * 收到消息后处理
	 * @see \Server\EGBaseServer::onReceive()
	 */
	public function onReceive($server,$fd,$fromId,$data) {
		//拆包
		$packData = new EGSockBuffer();
		$packData->setData($data);

		//事件
		$event = '';
		//秘钥
		$secretKey = '';
		
		switch ($event) {
			//gateway连接
			case 'gatewayConnect':
				{
					if ($secretKey != $this->_secretKey) {
						EGLog::warn("Register: Key does not match $secretKey !== {$this->_secretKey}");
						$this->serverClose($fd);
						return;
					}
					//保存当前连接
					
					//判断地址存不存在，不存在的话关闭该连接
					$this->broadcastAddresses();
				}
				break;
			//worker连接
			case 'workerServerConnect':
				{
					
					if ($secretKey != $this->_secretKey) {
						EGLog::warn("Register: Key does not match $secretKey !== {$this->_secretKey}");
						$this->serverClose($fd);
						return;
					}
					//保存当前连接
					$this->broadcastAddresses($fd);
				}
				break;
			case 'ping':
				break;
			default:
				break;
		}
	}
	
	public function onClose($server,$fd,$fromId) {
	
	}
	
	/**
	 * 向woker服务器广播gateway内部通讯地址
	 * @param string $connection
	 */
	public function broadcastAddresses($connection = null) {
		
	}
}
