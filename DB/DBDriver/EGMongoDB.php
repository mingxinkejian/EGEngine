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
	/*
	 * (non-PHPdoc) @see \DB\EGIDB::lazyConnection()
	 */
	protected function lazyConnection() {
		// TODO Auto-generated method stub
		$hostName = 'mongodb://'.($this->_config['username']?"{$this->_config['username']}":'').($this->_config['password']?":{$this->_config['password']}@":'').$this->_config['masterName'].($this->_config['hostport']?":{$this->_config['hostport']}":'').'/'.($this->_config['database']?"{$this->_config['database']}":'');    
	}
	
	/*
	 * (non-PHPdoc) @see \DB\EGIDB::nowConnection()
	 */
	protected function nowConnection() {
		// TODO Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc) @see \DB\EGIDB::connection()
	 */
	public function connection() {
		// TODO Auto-generated method stub
		if ($this->_config['connType']=='lazy'){
			$this->lazyConnection();
		}else{
			$this->connection();
		}
	}
	

	/* (non-PHPdoc)
     * @see \DB\EGIDB::close()
     */
    public function close()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::freeResult()
     */
    public function freeResult()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::getDBError()
     */
    public function getDBError()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::getLastId()
     */
    public function getLastId()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::getLastSql()
     */
    public function getLastSql()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \DB\EGIDB::selectDB()
     */
    public function selectDB($dbName)
    {
        // TODO Auto-generated method stub
        
    }

	/**
	 * 切换集合
	 * @param string $collection
	 * @param string $dbName
	 * @param boolean $isMaster
	 */
	public function switchCollection($collection,$dbName,$isMaster=true){
	    
	}
}
