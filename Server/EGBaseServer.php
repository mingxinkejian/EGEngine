<?php

namespace Server;
use Log\EGLog;
/*
 * Server基类
 */
class EGBaseServer {
	protected $_defaultPort;
	protected $_defaultHost;
	public $_server;
	protected $_config;
	protected $_debug = false;

	/**
	 * 设置debug
	 *
	 * @param string $debug        	
	 */
	public function setDebug($debug = false) {
		$this->_debug = $debug;
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
	 * 添加监听的地址和端口
	 *
	 * @param unknown $host        	
	 * @param unknown $port        	
	 * @param string $type        	
	 */
	public function addListener($host, $port, $type = SWOOLE_SOCK_TCP) {
		if ($this->_server) {
			return $this->_server->listen ( $host, $port, $type );
		}
		return null;
	}
	
	/**
	 * Server启动在主进程的主线程回调此函数
	 * 在此事件之前Swoole Server已进行了如下操作
	 * 已创建了manager进程
	 * 已创建了worker子进程
	 * 已监听所有TCP/UDP端口
	 * 已监听了定时器
	 * 
	 * 接下来要执行
	 * 主Reactor开始接收事件，客户端可以connect到Server
	 * onStart回调中，仅允许echo、打印Log、修改进程名称。不得执行其他操作。onWorkerStart和onStart回调是在不同进程中并行执行的，不存在先后顺序。
	 * 可以在onStart回调中，将$serv->master_pid和$serv->manager_pid的值保存到一个文件中。这样可以编写脚本，向这两个PID发送信号来实现关闭和重启的操作
	 * 
	 * 在onStart中创建的全局资源对象不能在worker进程中被使用，因为发生onStart调用时，worker进程已经创建好了。
	 * 新创建的对象在主进程内，worker进程无法访问到此内存区域。
	 * 因此全局对象创建的代码需要放置在swoole_server_start之前
	 * @param unknown $server
	 */
	public function onStart($server) {
		
	}
	
	/**
	 * 此事件在Server结束时发生
	 * 在此之前Swoole Server已进行了如下操作
	 * 已关闭所有线程
	 * 已关闭所有worker进程
	 * 已close所有TCP/UDP监听端口
	 * 已关闭主Rector
	 * 强制kill进程不会回调onShutdown，如kill -9
	 * 需要使用kill -15来发送SIGTREM信号到主进程才能按照正常的流程终止
	 * @param unknown $server        	
	 */
	public function onShutdown($server) {
		if ($this->_debug) {
			EGLog::debug ( "server shutdown" );
		}
	}
	
	/**
	 * 此事件在worker进程/task进程启动时发生。这里创建的对象可以在进程生命周期内使用
	 * 通过$workerId参数的值来，判断worker是普通worker还是task_worker。$workerId>= $serv->setting['worker_num'] 时表示这个进程是task_worker
	 * 如果想使用swoole_server_reload实现代码重载入，必须在workerStart中require你的业务文件，而不是在文件头部。在onWorkerStart调用之前已包含的文件，不会重新载入代码。
	 * 可以将公用的，不易变的php文件放置到onWorkerStart之前。这样虽然不能重载入代码，但所有worker是共享的，不需要额外的内存来保存这些数据。
	 * onWorkerStart之后的代码每个worker都需要在内存中保存一份
	 * @param unknown $server
	 * @param unknown $workerId
	 */
	public function onWorkerStart($server, $workerId) {
		// swoole_set_process_name('EGServer_worker');
		echo "WorkerStart: MasterPid={$server->master_pid}|Manager_pid={$server->manager_pid}|WorkerId={$server->worker_id}|WorkerPid={$server->worker_pid}".PHP_EOL;
		if ($this->_debug) {
			EGLog::debug ( "WorkerStart: MasterPid={$server->master_pid}|Manager_pid={$server->manager_pid}|WorkerId={$server->worker_id}|WorkerPid={$server->worker_pid}" );
		}
	}
	
	/**
	 * 此事件在worker进程终止时发生。在此函数中可以回收worker进程申请的各类资源
	 * @param unknown $server
	 * @param unknown $workerId
	 */
	public function onWorkerStop($server,$workerId) {
		
	}
	
	/**
	 * 有新的连接进入时，在worker进程中回调
	 * $server是swoole_server对象
	 * $fd是连接的文件描述符，发送数据/关闭连接时需要此参数
	 * $fromId来自那个Reactor线程
	 * 
	 * onConnect/onClose这2个回调发生在worker进程内，而不是主进程。
	 * UDP协议下只有onReceive事件，没有onConnect/onClose事件
	 * 
	 * 当设置dispatch_mode = 1/3时会自动去掉onConnect/onClose事件回调
	 * 在此模式下onConnect/onReceive/onClose可能会被投递到不同的进程。连接相关的PHP对象数据，无法实现在onConnect回调初始化数据，onClose清理数据
	 * onConnect/onReceive/onClose 3种事件可能会并发执行，可能会带来异常
	 * @param unknown $server
	 * @param unknown $fd
	 * @param unknown $fromId
	 */
	public function onConnect($server,$fd,$fromId) {
		
	}
	
	/**
	 * 接收到数据时回调此函数，发生在worker进程中
	 * 
	 * $server，swoole_server对象
	 * $fd，TCP客户端连接的文件描述符
	 * $from_id，TCP连接所在的Reactor线程ID
	 * $data，收到的数据内容，可能是文本或者二进制内容
	 * 开启swoole的自动协议选项，onReceive回调函数单次收到的数据最大为64K
	 * Swoole支持二进制格式，$data可能是二进制数据
	 * 
	 * UDP协议，onReceive可以保证总是收到一个完整的包，最大长度不超过64K
	 * UDP协议下，$fd参数是对应客户端的IP，$from_id是客户端的端口
	 * TCP协议是流式的，onReceive无法保证数据包的完整性，可能会同时收到多个请求包，也可能只收到一个请求包的一部分数据
	 * 
	 * @param unknown $server
	 * @param unknown $fd
	 * @param unknown $fromId
	 * @param unknown $data
	 */
	public function onReceive($server,$fd,$fromId,$data) {
		
	}
	
	/**
	 * 接收到UDP数据包时回调此函数，发生在worker进程中
	 * $server，swoole_server对象
	 * $data，收到的数据内容，可能是文本或者二进制内容
	 * $client_info，客户端信息包括address/port/server_socket 3项数据
	 * 服务器同时监听TCP/UDP端口时，收到TCP协议的数据会回调onReceive，收到UDP数据包回调onPacket
	 * 如果未设置onPacket回调函数，收到UDP数据包默认会回调onReceive函数
	 * 
	 * onPacket回调可以通过计算得到onReceive的$fd和$reactor_id参数值。计算方法如下：
	 * $fd = unpack('L', pack('N', ip2long($addr['address'])))[1];
	 * $reactor_id = ($addr['server_socket'] << 16) + $addr['port'];
	 * 
	 * @param unknown $server
	 * @param unknown $data
	 * @param unknown $clientInfo
	 */
	public function onPacket($server,$data,$clientInfo) {
		
	}
	
	/**
	 * $server是swoole_server对象
	 * $fd是连接的文件描述符
	 * $fromId来自那个reactor线程
	 * onClose回调函数如果发生了致命错误，会导致连接泄漏。通过netstat命令会看到大量CLOSE_WAIT状态的TCP连接
	 * 无论由客户端发起close还是服务器端主动调用$serv->close()关闭连接，都会触发此事件。因此只要连接关闭，就一定会回调此函数
	 * 调用connection_info方法获取到连接信息，在onClose回调函数执行完毕后才会调用close关闭TCP连接
	 * @param unknown $server
	 * @param unknown $fd
	 * @param unknown $fromId
	 */
	public function onClose($server,$fd,$fromId) {
		
	}
	
	/**
	 * $workId是异常进程的编号
	 * $workerPid是异常进程的ID
	 * $exitCode退出的状态码，范围是 1 ～255
	 * 此函数主要用于报警和监控，一旦发现Worker进程异常退出，那么很有可能是遇到了致命错误或者进程CoreDump。通过记录日志或者发送报警的信息来提示开发者进行相应的处理。
	 * @param unknown $server
	 * @param unknown $workId
	 * @param unknown $workerPid
	 * @param unknown $exitCode
	 */
	public function onWorkerError($server,$workId,$workerPid,$exitCode) {
		
	}
	
	/**
	 * 当管理进程启动时调用它，在这个回调函数中可以修改管理进程的名称
	 * 注意manager进程中不能添加定时器
	 * manager进程中可以调用task功能
	 * @param unknown $server
	 */
	public  function onManagerStart($server) {
		
	}
	
	/**
	 * 当管理进程结束时调用它
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
		return $this->_server->close ( $fd );
	}
	
}
