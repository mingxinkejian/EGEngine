<?php

namespace Cache;

class EGCacheFactory {
	const REDIS = 'Redis';
	const MEMCACHED = 'Memcached';
	static $_instances = array ();
	static $_configs = array ();
	/**
	 * 获取对应实例
	 * 
	 * @param unknown $type        	
	 */
	public static function getInstance($config,$type = self::REDIS) {
		if (empty($config)){
			return null;
		}
		$name = $config['name'];		
		
		if (empty(self::$_instances[$name])){
			$class  =   strpos($type,'\\')? $type : 'Cache\\CacheDriver\\EG'.$type;
			$instance=new $class();
			$instance->connection($config);
			
			self::$_configs[$name]=$config;
			self::$_instances[$name]=$instance;
		}

		return self::$_instances[$name];
	}
	/**
	 * 删除实例
	 * @param unknown $names
	 */
	public static function destoryInstance($names = array()) {
		if (empty ( self::$_instances )) {
			return true;
		}
		
		if (empty($names)){
			return true;
		}
		
		foreach ($names as $name) {
			unset(self::$_instances[$name]);
			unset(self::$_configs[$name]);
		}
		return true;
	}
}
