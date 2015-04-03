<?php

namespace DB\DBDriver;

use DB\EGADB;

class EGMongoDB extends EGADB {

	protected $_mongo           =   null; // MongoDb Object
	protected $_collection      =   null; // MongoCollection Object
	protected $_dbName          =   ''; // dbName
	protected $_collectionName  =   ''; // collectionName
	protected $_cursor          =   null; // MongoCursor Object
	protected $comparison       =   ['neq'=>'ne','ne'=>'ne','gt'=>'gt','egt'=>'gte','gte'=>'gte','lt'=>'lt','elt'=>'lte','lte'=>'lte','in'=>'in','not in'=>'nin','nin'=>'nin'];
	
	public function __construct($config) {
		$this->_config = $this->parseConfig ( $config );
	}
	/*
	 * (non-PHPdoc) @see \DB\EGIDB::lazyConnection()
	 */
	protected function lazyConnection() {
		// TODO Auto-generated method stub
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
			$instance->lazyConnection();
		}else{
			$instance->connection();
		}
	}
}
