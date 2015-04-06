<?php

namespace DB;

interface EGIDB {
	
	/**
	 * 懒加载，只有对数据库做操作时才连接
	 */
	protected function lazyConnection();
	
	/**
	 * 初始化时连接数据库
	 */
	protected function nowConnection();
	
	/**
	 * 连接数据库
	 */
	public function connection();
	
	/**
	 * 选择默认数据库
	 * @param string $dbName
	 */
	public function selectDB($dbName);

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
}
