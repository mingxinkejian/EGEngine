<?php

namespace Extension\DataFrame;

class EGByteProcessor {
	//大端
	const BIG_ENDIAN = 0x1;
	//小端
	const LITTLE_ENDIAN = 0x2;
	
	//0x00
	const FIND_NUL = 0;
	//0x00
	const FIND_NON_NUL = 0;
	const FIND_CR = "\r";
	const FIND_LF = "\n";
	const FIND_CRLF = "\r\n";
	const FIND_SEMI_COLON = ";";
	
	public function process($value,$isOrNot) {
		if ($isOrNot) {
			
		}
	}
	/**
	 * 获取当前操作系统是大端还是小端
	 */
	public static function nativeOrder() {
		$testData = pack('L',0x12345678);
		$hex = bin2hex($testData);
		if (ord(pack('H2',$hex)) === 0x78) {
			return EGByteProcessor::BIG_ENDIAN;
		}else {
			return EGByteProcessor::LITTLE_ENDIAN;
		}
	}
}
