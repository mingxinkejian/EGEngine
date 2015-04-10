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
	        echo "can not found mongoClient \n";
	        return ;
	    }
	    if(empty($config)){
	        echo "config is null,please confirm config \n";
	        return ;
	    }
		$this->_config = $this->parseConfig ( $config );
	}

	public function connection($config,$connId=0){
		// TODO Auto-generated method stub
		$host = 'mongodb://'.($config['username']?"{$config['username']}":'').($config['password']?":{$config['password']}@":'').$config['masterName'].($config['hostPort']?":{$config['hostPort']}":'').'/'.($config['database']?"{$config['database']}":'');
		try{
			$this->_handlerList[$connId] = new \mongoClient( $host,$config['options']);
		}catch (\MongoConnectionException $e){
			echo "connect mongoClient failed :".$e->getMessage()."\n";
		}
		return $this->_handlerList[$connId];
	}
	
	/* 
	 * 分布式连接数据库，可以使用主从分离，主数据库写，从数据库读
	 */
	public function multiConnection($isMaster = true) {
		// TODO Auto-generated method stub
		//是否读写分离
		$masterIndex=0;
		if ($this->_config['rwSeparate']){
			if ($isMaster){
				$slaveIndex=$masterIndex;
			}else{
				// 读操作连接从服务器
				$slaveIndex = floor(mt_rand(1,count($this->_config['hostName'])-1));   // 每次随机连接的数据库
			}
		}else{
			// 读写操作不区分服务器，每次随机连接的数据库
			$slaveIndex = floor(mt_rand(0,count($this->_config['hostName'])-1));
		}
		$config['username']=$this->_config['username'];
		$config['password']=$this->_config['password'];
		$config['masterName']=$this->_config['hostName'][$slaveIndex];
		$config['hostPort']=$this->_config['hostPort'];
		$config['database']=$this->_config['database'];
		$config['options']=$this->_config['options'];
		
		return $this->connection($config);
	}

	/**
	 * 切换集合
	 * @param string $collection
	 * @param string $dbName
	 */
	public function switchCollection($collection,$dbName='',$isMaster=true){
	    if (!$this->_handler){
	    	$this->initConnection($isMaster);
	    }
	    try {
	    	//如果dbName不为空的话切换数据库
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
		$this->queryStr   =  $this->_dbName.'.'.$this->_collectionName.'.insert(';
		$this->queryStr   .= $data?json_encode($data):'{}';
		$this->queryStr   .= ')';
		
		try {
			$result =  $replace?   $this->_collection->save($data):  $this->_collection->insert($data);
			if($result) {
				$_id    = $data['_id'];
				if(is_object($_id)) {
					$_id = $_id->__toString();
				}
				$this->_lastId  = $_id;
			}
			return $result;
		} catch (\MongoCursorException $e) {
			echo $e->getMessage();
		}
	}


	public function insertArray($dataList, $options = array(), $replace = false) {
		// TODO Auto-generated method stub
		try {
			$result =  $this->_collection->batchInsert($dataList);
			return $result;
		} catch (\MongoCursorException $e) {
			echo $e->getMessage();
		}

	}


	public function selectInsert($fields, $table, $options = array()) {
		// TODO Auto-generated method stub
		echo 'mongoDB have not selectInsert';
	}


	public function update($data, $options = array()) {
		// TODO Auto-generated method stub
		try {
			$multiple   =   $options['multiple'];
			unset($options);
			$this->queryStr   =  $this->_dbName.'.'.$this->_collectionName.'.update(';
			$this->queryStr   .= $options?json_encode($options):'{}';
			$this->queryStr   .=  ','.json_encode($data).')';
			
			$result   = $this->_collection->update($options,$data,$multiple);
			return $result;
		} catch (\MongoCursorException $e) {
			echo $e->getMessage();
		}
	}


	public function delete($options = array()) {
		// TODO Auto-generated method stub
		try{
			$this->queryStr   =  $this->_dbName.'.'.$this->_collectionName.'.remove('.json_encode($options).')';
			$result   = $this->_collection->remove($options);
			return $result;
		} catch (\MongoCursorException $e) {
			echo $e->getMessage();
		}
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
	
	public function findAndModify($options=array()){
		
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
