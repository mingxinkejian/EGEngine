<?php

namespace DB\DBDriver;

use DB\EGADB;
class EGMySqlDB extends EGADB{

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
	 * @see \DB\EGIDB::singleConnection()
	 */
	public function singleConnection(){
		
	}
	
	/* 
	 * 主库写，从库读
	 * @see \DB\EGIDB::multipleConnection()
	 */
	public function multiConnection(){
		
	}

	public function selectDB($dbName) {
		// TODO Auto-generated method stub
		
	}


	public function query($sql, $fetchSql = false) {
		// TODO Auto-generated method stub
		
	}


	public function execute($sql, $fetchSql = false) {
		// TODO Auto-generated method stub
		
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
		
	}


	public function select($options = array()) {
		// TODO Auto-generated method stub
		
	}


	public function count($options = array()) {
		// TODO Auto-generated method stub
		
	}


	public function distanct($options = array()) {
		// TODO Auto-generated method stub
		
	}


	public function freeResult() {
		// TODO Auto-generated method stub
		
	}


	public function close() {
		// TODO Auto-generated method stub
		
	}


	public function getDBError() {
		// TODO Auto-generated method stub
		
	}


	public function getLastSql() {
		// TODO Auto-generated method stub
		
	}


	public function getLastId() {
		// TODO Auto-generated method stub
		
	}


	public function getTables() {
		// TODO Auto-generated method stub
		
	}
	
	
}
