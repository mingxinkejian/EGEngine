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
	public static function getInstance($configData,$type = self::REDIS) {
		if (empty($configData)){
			return null;
		}
		$md5    =   md5(serialize($configData));
		$name = $md5;
		
		if (empty(self::$_instances[$name])){
			$class  =   strpos($type,'\\')? $type : 'Cache\\CacheDriver\\EG'.$type;
			$instance=new $class();
			$instance->connection($configData);
			
			self::$_configs[$name]=$configData;
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
