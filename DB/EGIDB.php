<?php

namespace DB;

interface EGIDB {
	/**
	 * 连接数据库
	 */
	public function connection();
	
	/**
	 * 单台数据库连接
	 */
	public function singleConnection();
	
	/**
	 * 默认采用主库写，从库读，可以在配置文件中设置
	 * 分布式数据库连接
	 */
	public function multiConnection();
	
	/**
	 * 选择默认数据库
	 * @param string $dbName
	 */
	public function selectDB($dbName);
	
	/**
	 * 执行查询 返回数据集
	 * @param string $sql
	 * @param string $fetchSql
	 */
	public function query($sql,$fetchSql=false);
	
	/**
	 * 执行语句
	 * @param string $sql
	 * @param string $fetchSql
	 */
	public function execute($sql,$fetchSql=false);
	
	/**
	 * 插入数据
	 * @param unknown $data
	 * @param unknown $options
	 * @param string $replace
	 */
	public function insert($data,$options=array(),$replace=false);
	
	/**
	 * 批量插入数据
	 * @param unknown $dataSet
	 * @param unknown $options
	 * @param string $replace
	 */
	public function insertArray($dataSet,$options=array(),$replace=false);
	
	/**
	 * 通过Select方式插入记录
	 * @param unknown $fields
	 * @param unknown $table
	 * @param unknown $options
	 */
	public function selectInsert($fields,$table,$options=array());
	
	/**
	 * 更新数据
	 * @param unknown $data
	 * @param unknown $options
	 */
	public function update($data,$options=array());
	
	/**
	 * 删除数据
	 * @param unknown $options
	 */
	public function delete($options=array());
	
	/**
	 * 清空表数据
	 * @param unknown $options
	 */
	public function clearTable($options=array());
	
	/**
	 * 查询数据
	 * @param unknown $options
	 */
	public function select($options=array());
	
	/**
	 * 统计数量
	 * @param unknown $options
	 */
	public function count($options=array());
	
	/**
	 * 列出不同值
	 * @param unknown $options
	 */
	public function distinct($options=array());
	/**
	 * 释放查询结果
	 */
	public function freeResult();
	/**
	 * 关闭数据库连接
	*/
	public function close();
	
	/**
	 * 获取数据库错误
	*/
	public function getDBError();
	
	/**
	 * 获取上一次执行sql的语句
	*/
	public function getLastSql();
	
	/**
	 * 获取上一次插入数据的主键Id
	*/
	public function getLastId();
	/**
	 * 取得当前数据库的表信息
	 */
	public function getTables();
}
