<?php

namespace Server;

abstract class EGBaseServer implements EGIServer{
	protected $_defaultPort;
	protected $_defaultHost;
	protected $_server;
	protected $_logger;
	protected $_config;
	
	public function setLogger($logger) {
		$this->_logger = $logger;
	}
	
	/**
	 * 读取配置文件，在对应服务器的子类中覆盖
	 * @param unknown $fileName
	 */
	public function loadConfig($fileName){}
	/**
	 * 启动服务器
	 */
	public function startServer(){
		$this->_server->start();
	}
	/**
	 * 启动
	 *
	 * @param unknown $server
	*/
	public function onStart($server){
		
	}
	/**
	 * 连接
	 *
	 * @param unknown $server
	 * @param unknown $clientId
	 * @param unknown $fromId
	*/
	public function onConnect($server, $clientId, $fromId){
		
	}
	/**
	 * 接收
	 *
	 * @param unknown $server
	 * @param unknown $clientId
	 * @param unknown $fromId
	 * @param unknown $data
	*/
	public function onReceive($server, $clientId, $fromId, $data){
		
	}
	/**
	 * 关闭
	 *
	 * @param unknown $server
	 * @param unknown $clientId
	 * @param unknown $fromId
	*/
	public function onClose($server, $clientId, $fromId){
		
	}
	/**
	 * 停止
	 *
	 * @param unknown $server
	*/
	public function onShutdown($server){
		
	}
}
