<?php

namespace Server;

abstract class EGBaseServer {
	protected $_defaultPort;
	protected $_defaultHost;
	protected $_server;
	protected $_logger;
	protected $_config;
	
	protected $_debug=false;
	
	public function getLogger(){
		return $this->_logger;
	}
	/**
	 * 设置debug
	 * @param string $debug
	 */
	public function setDebug($debug=false){
		$this->_debug=$debug;
	}
	/**
	 * 服务器日志打印
	 * @param string $logMsg
	 */
	public function printLog($logMsg=''){
		$this->_logger->printLog($logMsg);
	}
	/**
	 * 读取配置文件，在对应服务器的子类中覆盖
	 * @param unknown $fileName
	 */
	public function loadConfig($config){}
	/**
	 * 启动服务器
	 */
	public function startServer(){
		$this->_server->start();
	}
	
	/**
	 * 获取服务器实例
	 */
	public function getServer(){
		return $this->_server;
	}
	
	/**
	 * 添加监听的地址和端口
	 * @param unknown $host
	 * @param unknown $port
	 * @param string $type
	 */
	public function addListener($host,$port,$type=SWOOLE_SOCK_TCP){
		if ($this->_server){
			$this->_server->addListener($host, $port,$type);
		}
	}
	/**
	 *
	 * @param $server
	 * @param $clientId 是连接的文件描述符
	 * @param $fromId 来自那个reactor线程
	 */
	public abstract function onClose($server, $clientId, $fromId);
	/**
	 * 工作进程
	 * @param $server
	 * @param $workerId
	 */
	public function onWorkerStart($server,$workerId){
		// 		swoole_set_process_name('EGServer_worker');
		if ($this->_debug){
			$this->_logger->debug( "WorkerStart: MasterPid={$server->master_pid}|Manager_pid={$server->manager_pid}|WorkerId={$server->worker_id}|WorkerPid={$server->worker_pid}");
		}
		
	}
	
	/**
	 * 停止
	 *
	 * @param unknown $server
	*/
	public function onShutdown($server){
		if ($this->_debug){
			$this->_logger->debug("server shutdown");
		}
	}
	/*
	 * 添加监听的回调函数
	 */
	public function addFuncCallBack($method,$callback){
		$this->_server->on($method,$callback);
	}
}
