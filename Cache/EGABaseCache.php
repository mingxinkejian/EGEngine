<?php

namespace Cache;

class EGABaseCache {
	
	protected $_handler;
	
	
	/**
	 * 魔术方法—调用Cache对应的特定方法
	 * @param unknown $method
	 * @param unknown $args
	 * @return void|mixed
	 */
	public function __call($method,$args){
		//调用缓存类型自己的方法
		if(method_exists($this->_handler, $method)){
			return call_user_func_array(array($this->_handler,$method), $args);
		}else{
			return;
		}
	}
}
