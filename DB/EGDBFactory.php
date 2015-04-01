<?php

namespace DB;

class EGDBFactory {
	
	const DBTYPE_MYSQL='MySql';
	const DBTYPE_MONGODB='Mongo';
	
	static $_instances=array();
	static $_configs=array();
	
	public static function getInstance($config,$type=self::DBTYPE_MYSQL){
		if (empty($config)){
			return null;
		}
		
		$name = $config['name'];
		
		if (empty(self::$_instances[$name])){
			$class  =   strpos($type,'\\')? $type : 'DB\\DBDriver\\EG'.$type;
			$instance=new $class();	
			self::$_configs[$name]=$config;
			self::$_instances[$name]=$instance;
			
			if ($config['connType']=='lazy'){
				$instance->lazyConnection($config);
			}else{
				$instance->connection($config);
			}
		}
		
		return self::$_instances[$name];
	}
}
