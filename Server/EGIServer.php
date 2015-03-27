<?php

namespace Server;

interface EGIServer {
	
	/**
	 * 启动服务器
	 */
	public function startServer();
	/**
	 * 启动
	 * 
	 * @param unknown $server        	
	 */
	public function onStart($server);
	/**
	 * 连接
	 * 
	 * @param unknown $server        	
	 * @param unknown $clientId        	
	 * @param unknown $fromId        	
	 */
	public function onConnect($server, $clientId, $fromId);
	/**
	 * 接收
	 * 
	 * @param unknown $server        	
	 * @param unknown $clientId        	
	 * @param unknown $fromId        	
	 * @param unknown $data        	
	 */
	public function onReceive($server, $clientId, $fromId, $data);
	/**
	 * 关闭
	 * 
	 * @param unknown $server        	
	 * @param unknown $clientId        	
	 * @param unknown $fromId        	
	 */
	public function onClose($server, $clientId, $fromId);
	/**
	 * 停止
	 * 
	 * @param unknown $server        	
	 */
	public function onShutdown($server);
}
