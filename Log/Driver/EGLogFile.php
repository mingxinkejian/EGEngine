<?php

namespace Log\Driver;

class EGLogFile {
	private $_logRoot;
	private $_fileSize = 2097152;
	public function __construct($logRoot) {
		$this->_logRoot = $logRoot;
	}
	
	/**
	 * 日志写入接口
	 * 
	 * @access public
	 * @param string $log 日志信息      	
	 * @param string $destination 写入目标
	 * @return void
	 */
	public function write($log, $destination = '') {
		$now = date ( 'c' );
		if (empty ( $destination ))
			$destination = $this->_logRoot . date ( 'y_m_d' ) . '.log';
			// 检测日志文件大小，超过配置大小则备份日志文件重新生成
		if (is_file ( $destination ) && floor ( $this->_fileSize ) <= filesize ( $destination )) {
			rename ( $destination, dirname ( $destination ) . '/' . time () . '-' . basename ( $destination ) );
		}
		error_log ( "[{$now}] " . $_SERVER ['REMOTE_ADDR'] . ' ' . $_SERVER ['REQUEST_URI'] . "\r\n{$log}\r\n", 3, $destination );
	}
}
