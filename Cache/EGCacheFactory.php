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
	}
	/**
	 * 删除实例，并关闭连接
	 * @param unknown $names
	 */
	public static function destoryInstance($names = array()) {
		if (empty ( self::$_instances )) {
			return true;
		}
		
		if (empty ( $names )) {
			foreach ( self::$_instances as $name => $redis ) {
				if (self::$_configs [$name] ['pconnect']) {
					continue;
				}
				$redis->close ();
				unset ( self::$_configs [$name] );
			}
		} else {
			foreach ( $names as $name ) {
				if (isset ( self::$_instances [$name] )) {
					if (self::$_configs [$name] ['pconnect']) {
						continue;
					}
					self::$_instances [$name]->close ();
					unset ( self::$_configs [$name] );
				}
			}
		}
		
		return true;
	}
}
