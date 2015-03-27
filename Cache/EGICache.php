<?php

namespace Cache;

interface EGICache {
	
	public function addCache($key,$value,$config=array());
	
	public function setCache($key,$value,$config=array());
	
	public function getCache($key);
	
	public function delete($key);
	
	/*
	 * 队列缓存
	*/
	public function queue($key);
	
	public function increment($key, $step = 1);
	
	public function decrement($key, $step = 1);
	
	public function clear();
}
