<?php

namespace DB;

interface EGIDB {
	
	/**
	 * 懒加载，只有对数据库做操作时才连接
	 */
	public function lazyConnection($config);
	
	/**
	 * 连接数据库
	 */
	public function connection($config);
}
