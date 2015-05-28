<?php

namespace Log;

class EGLog {
	protected $_logRoot;
	protected $_logger;
	const ERROR=1;
	const WARN=2;
	const DEBUG=3;
	const INFO=4;
	
	const TYPE_FILE='EGLogFile';
	const TYPE_MYSQL='EGLogMySql';
	
	public function __construct($logDir,$logType=self::TYPE_FILE){
		$this->_logRoot=$logDir;
		$className='\\Log\\Driver\\'.$logType;
		$this->_logger=new $className($logDir);
	}
	
	public function printLog($logMsg){
		echo $logMsg."\n";
	}
	
	public function debug($debugMsg){
		$logMsg="[DEBUG] ".date('Y-M-d H:i:s').' '.$debugMsg;
		$this->_logger->write($logMsg);
	}
	
	public function warn($warnMsg){
		$logMsg="[WARN] ".date('Y-M-d H:i:s').' '.$warnMsg;
		$this->_logger->write($logMsg);
	}
	public function error($errorMsg){
		$logMsg="[ERROR] ".date('Y-M-d H:i:s').' '.$errorMsg;
		$this->_logger->write($logMsg);
	}
	public function info($infoMsg){
		$logMsg="[INFO] ".date('Y-M-d H:i:s').' '.$infoMsg;
		$this->_logger->write($logMsg);
	}
}
