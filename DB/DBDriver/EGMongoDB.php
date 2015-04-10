<?php

namespace DB\DBDriver;

use DB\EGADB;

class EGMongoDB extends EGADB {

	protected $_mongo           =   null; // MongoDb Object
	protected $_collection      =   null; // MongoCollection Object
	protected $_dbName          =   ''; // dbName
	protected $_collectionName  =   ''; // collectionName
	protected $_cursor          =   null; // MongoCursor Object
	protected $comparison       =   array('neq'=>'ne','ne'=>'ne','gt'=>'gt','egt'=>'gte','gte'=>'gte','lt'=>'lt','elt'=>'lte','lte'=>'lte','in'=>'in','not in'=>'nin','nin'=>'nin');
	
	public function __construct($config) {
	    if (!extension_loaded('mongoClient')){
	        echo 'can not found mongoClient';
	        return ;
	    }
	    if(empty($config)){
	        echo 'config is null,please confirm config';
	        return ;
	    }
		$this->_config = $this->parseConfig ( $config );
	}
	/**
	 * 连接数据库
	 */
	public function connection(){
		// TODO Auto-generated method stub
		if ($this->_config['connType']=='multi'){
			$this->multipleConnection();
		}else{
			$this->singleConnection();
		}
	}
	
	/* 
	 * 单台服务器连接
	 * @see \DB\EGIDB::initConnection()
	 */
	public function singleConnection(){
		$hostName = 'mongodb://'.($this->_config['username']?"{$this->_config['username']}":'').($this->_config['password']?":{$this->_config['password']}@":'').$this->_config['masterName'].($this->_config['hostport']?":{$this->_config['hostport']}":'').'/'.($this->_config['database']?"{$this->_config['database']}":'');
		try {
			$this->_handler= new \mongoClient( $hostName,$this->_config['options']);
		} catch (\MongoConnectionException $e) {
			echo $e->getMessage();
		}
	}
	
	/* 
	 * 主库写，从库读
	 * @see \DB\EGIDB::multipleConnection()
	 */
	public function multiConnection(){
		
	}

	/**
	 * 切换集合
	 * @param string $collection
	 * @param string $dbName
	 */
	public function switchCollection($collection,$dbName=''){
	    if (!$this->_handler){
	    	$this->connection();
	    }
	    try {
	    	if (empty($dbName)){
	    		// 当前MongoDb对象
	    		$this->_dbName  =  $dbName;
	    		$this->_mongo = $this->selectDb($dbName);
	    	}
	    	if($this->_collectionName != $collection) {
	    		$this->_collection =  $this->_mongo->selectCollection($collection);
	    		$this->_collectionName  = $collection; // 记录当前Collection名称
	    	}
	    } catch (\MongoException $e) {
	    	echo $e->getMessage();
	    }
	    
	}

	public function selectDB($dbName) {
		// TODO Auto-generated method stub
		return $this->_handler->selectDb($dbName);
	}


	public function query($sql, $fetchSql = false) {
		// TODO Auto-generated method stub
		
	}

	/*
	 * Runs JavaScript code on the database server.
	 */
	public function execute($code, $options=array()) {
		// TODO Auto-generated method stub
		$this->_sql = 'execute:'.$code;
		$result   = $this->_mongo->execute($code,$options);
		$this->debug(false);
		if($result['ok']) {
			return $result['retval'];
		}else{
			echo 'execute run error!';
			return false;
		}
	}
	
	public function commond($command=array()){
		$this->_sql = 'command:'.json_encode($command);
		$result   = $this->_mongo->command($command);
		if(!$result['ok']) {
			echo 'commond run error!';
			return false;
		}
		return $result;
	}


	public function insert($data, $options = array(), $replace = false) {
		// TODO Auto-generated method stub
		
	}


	public function insertArray($dataSet, $options = array(), $replace = false) {
		// TODO Auto-generated method stub
		
	}


	public function selectInsert($fields, $table, $options = array()) {
		// TODO Auto-generated method stub
		
	}


	public function update($data, $options = array()) {
		// TODO Auto-generated method stub
		
	}


	public function delete($options = array()) {
		// TODO Auto-generated method stub
		
	}


	public function clearTable($options = array()) {
		// TODO Auto-generated method stub
		try{
			$result   =  $this->_collection->drop();
			return $result;
		} catch (\MongoCursorException $e) {
			echo $e->getMessage();
		}
	}


	public function select($options = array()) {
		// TODO Auto-generated method stub
		
	}


	public function count($options = array()) {
		// TODO Auto-generated method stub
		
	}


	public function distinct($files,$args=array()) {
		// TODO Auto-generated method stub
		return $this->_collection->distinct($files,$args);
	}
	
	public function group($keys,$initial,$reduce,$options=[]){
		return $this->_collection->group($keys,$initial,$reduce,$options);
	}


	public function freeResult() {
		// TODO Auto-generated method stub
		$this->_cursor=null;
	}


	public function close() {
		// TODO Auto-generated method stub
		if($this->_handler) {
			$this->_handler->close();
			$this->_handler = null;
			$this->_mongo = null;
			$this->_collection =  null;
			$this->_cursor = null;
		}
	}


	public function getDBError() {
		// TODO Auto-generated method stub
		$this->_dbErr = $this->_mongo->lastError();
		return $this->_dbErr;
	}


	public function getLastSql() {
		// TODO Auto-generated method stub
		return $this->_sql;
	}


	public function getLastId() {
		// TODO Auto-generated method stub
		return $this->_lastId;
	}


	public function getTables() {
		// TODO Auto-generated method stub
		$this->_sql   =  $this->_dbName.'.getCollenctionNames()';
		$list   = $this->_mongo->listCollections();
		$info =  [];
		foreach ($list as $collection){
			$info[]   =  $collection->getName();
		}
		return $info;
	}

}