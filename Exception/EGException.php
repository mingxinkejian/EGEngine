<?php

namespace Exception;

class EGException extends \Exception{
	
	
	
	public static function fatalError(){
		//保存日志
		if ($e = error_get_last()) {
			switch($e['type']){
				case E_ERROR:
				case E_PARSE:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					ob_end_clean();
					break;
			}
		}
	}
	
	
	public static function appError(){
		
	}
	
	public static function appException(){
		
	}
}