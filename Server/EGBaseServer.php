<?php

namespace Server;
/*
 * Server基类
 */
class EGBaseServer {
	protected $_defaultPort;
	protected $_defaultHost;
	protected $_server;
	protected $_logger;
	protected $_config;
	protected $_debug = false;
	/**
	 * 获取日志控制器
	 */
	public function getLogger() {
		return $this->_logger;
	}
	/**
	 * 设置debug
	 *
	 * @param string $debug        	
	 */
	public function setDebug($debug = false) {
		$this->_debug = $debug;
	}
	/**
	 * 服务器日志打印
	 *
	 * @param string $logMsg        	
	 */
	public function printLog($logMsg = '') {
		$this->_logger->printLog ( $logMsg );
	}
	/**
	 * 读取配置文件，在对应服务器的子类中覆盖
	 *
	 * @param unknown $fileName        	
	 */
	public function loadConfig($config) {
		if (empty($config)){
			$config=array(
					//工作进程
					'worker_num' => 4,
					//是否守护进程
					'daemonize' => false,
					//最大请求数
					'max_request' => 10000,
					//工作模式
					'dispatch_mode' => 1
			);
		}
		$this->_config=$config;
		
		$this->_server->set($config);
	}
	/**
	 * 启动服务器
	 */
	public function startServer() {
		$this->_server->start ();
	}
	
	/**
	 * 获取服务器实例
	 */
	public function getServer() {
		return $this->_server;
	}
	
	/**
	 * 添加监听的地址和端口
	 *
	 * @param unknown $host        	
	 * @param unknown $port        	
	 * @param string $type        	
	 */
	public function addListener($host, $port, $type = SWOOLE_SOCK_TCP) {
		if ($this->_server) {
			$this->_server->addListener ( $host, $port, $type );
		}
	}
	
	/**
	 * server启动后回调
	 * @param unknown $server        	
	 */
	public function onStart($server) {
	}
	
	/**
	 * 停止
	 *
	 * @param unknown $server        	
	 */
	public function onShutdown($server) {
		if ($this->_debug) {
			$this->_logger->debug ( "server shutdown" );
		}
	}
	
	/**
	 * 工作进程
	 *
	 * @param
	 *        	$server
	 * @param
	 *        	$workerId
	 */
	public function onWorkerStart($server, $workerId) {
		// swoole_set_process_name('EGServer_worker');
		if ($this->_debug) {
			$this->_logger->debug ( "WorkerStart: MasterPid={$server->master_pid}|Manager_pid={$server->manager_pid}|WorkerId={$server->worker_id}|WorkerPid={$server->worker_pid}" );
		}
	}
	/**
	 * 此事件在worker进程终止时发生。在此函数中可以回收worker进程申请的各类资源
	 *
	 * @param unknown $server        	
	 * @param unknown $workerId        	
	 */
	public function onWorkerStop($server, $workerId) {
	}
	/**
	 * 定时器触发
	 *
	 * @param unknown $server        	
	 * @param unknown $interval        	
	 */
	public function onTimer($server, $interval) {
	}
	/**
	 * 有新的连接进入时，在worker进程中回调
	 * onConnect/onClose这2个回调发生在worker进程内，而不是主进程
	 * UDP协议下只有onReceive事件，没有onConnect/onClose事件
	 *
	 * @param unknown $server        	
	 * @param unknown $clientId        	
	 * @param unknown $fromId        	
	 */
	public function onConnect($server, $clientId, $fromId) {
	}
	/**
	 *
	 * @param unknown $server        	
	 * @param unknown $clientId        	
	 * @param unknown $fromId        	
	 * @param unknown $data
	 *        	收到的数据内容，可能是文本或者二进制内容
	 */
	public function onReceive($server, $clientId, $fromId, $data) {
	}
	
	/**
	 *
	 * @param
	 *        	$server
	 * @param $clientId 是连接的文件描述符        	
	 * @param $fromId 来自那个reactor线程        	
	 */
	public function onClose($server, $clientId, $fromId) {
	}
	/**
	 * 在task_worker进程内被调用。worker进程可以使用swoole_server_task函数向task_worker进程投递新的任务
	 * task进程的onTask事件中没有调用finish方法或者return结果。worker进程不会触发onFinish
	 *
	 * @param unknown $server        	
	 * @param unknown $taskId        	
	 * @param unknown $fromId        	
	 * @param unknown $data        	
	 */
	public function onTask($server, $taskId, $fromId, $data) {
	}
	
	/**
	 * 当worker进程投递的任务在task_worker中完成时，task进程会通过swoole_server->finish()方法将任务处理的结果发送给worker进程
	 *
	 * @param unknown $server        	
	 * @param unknown $taskId        	
	 * @param unknown $data        	
	 */
	public function onFinish($server, $taskId, $data) {
	}
	/**
	 * 当工作进程收到由sendMessage发送的管道消息时会触发onPipeMessage事件。worker/task进程都可能会触发onPipeMessage事件
	 *
	 * @param unknown $server        	
	 * @param unknown $workId        	
	 * @param unknown $message        	
	 */
	public function onPipeMessage($server, $workId, $message) {
	}
	/**
	 * 当worker/task_worker进程发生异常后会在Manager进程内回调此函数
	 *
	 * @param unknown $server        	
	 * @param unknown $workId        	
	 * @param unknown $workPid        	
	 * @param unknown $errCode        	
	 */
	public function onWorkerError($server, $workId, $workPid, $errCode) {
	}
	/**
	 * 在这个回调函数中可以修改管理进程的名称
	 * 注意manager进程中不能添加定时器
	 * manager进程中可以调用task功能
	 *
	 * @param unknown $server        	
	 */
	public function onManagerStart($server) {
	}
	/**
	 * 当管理进程结束时调用
	 *
	 * @param unknown $server        	
	 */
	public function onManagerStop($server) {
	}
	
	/**
	 * 关闭客户端
	 * @param unknown $fd
	 * @param number $from_id
	 */
	public function serverClose($fd, $from_id = 0){
		$this->_server->close ( $fd );
	}
	
}
