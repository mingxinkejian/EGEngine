<?php

namespace Extension\DataFrame;
use Extension\EGSockBuffer;
/**
 * 
 * |Do the most simple game's server framework
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |author mintingjian Date:2016-12-23 Time:下午5:11:00
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |	email:mingtingjian@sina.com                          
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Copyright (c) 2015 EasyGameEngine
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Desc:
 * 基于定长解码器
 * 假设接收到的报文如下：
 * +----+-----+---------+----+
 * | A | BC | DEFG | HI |
 * +----+-----+---------+----+
 * 当定长参数为3时，拆包与粘包的结果是：
 * +--------+-------+------+
 * | ABC | DEF | GHI |
 * +--------+-------+------+
 */
class EGFixedLengthFrameDecoder extends EGDataFrameDecoder{
	
	private $_packLen;
	private $_maxLength;
	
	public function __construct($maxLength,$packLen) {
		$this->_maxLength = $maxLength;
		$this->_packLen = $packLen;
	}
	
	/* (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::input()
	*/
	public function input($buffer) {
		// TODO Auto-generated method stub
		if (strlen($buffer) >= $this->_maxLength) {
			return -1;
		}
		
		//检查当前包长度
		$len = strlen($buffer);
		if($len < $this->_packLen) {
			return 0;
		}
		
		return $this->_packLen;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::encode()
	 */
	public function encode($data) {
	
		return $data;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::decode()
	 */
	public function decode($buffer) {
		
		//定长包分隔都是固定的
		return substr($buffer, 0,$this->_packLen);
	}
	
}
