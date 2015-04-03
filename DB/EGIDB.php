<?php

namespace DB;

interface EGIDB {
	
	/**
	 * 懒加载，只有对数据库做操作时才连接
	 */
	protected function lazyConnection();
	
	/**
	 * 连接数据库
	 */
	protected function nowConnection();
	
	public function connection();
}
