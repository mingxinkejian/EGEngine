<?php

namespace Extension\DataFrame;
/**
 * 
 * |Do the most simple game's server framework
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |author mintingjian Date:2016-12-23 Time:下午5:12:44
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |	email:mingtingjian@sina.com                          
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Copyright (c) 2015 EasyGameEngine
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Desc:
 * 基于分隔符解码器
 * 一行的结束标志包括： "\n" 和 "\r\n"
 */
class EGDelimiterBasedFrameDecoder extends EGDataFrameDecoder{
	private $_maxLength;
	private $_delimiter;
	/**
	 * 默认使用\n来作为分隔符
	 * @param unknown $maxLength
	 * @param unknown $delimiter
	 */
	public function __construct($maxLength,$delimiter = EGByteProcessor::FIND_LF) {
		$this->_maxLength = $maxLength;
		$this->_delimiter = $delimiter;
	}
	
	/* (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::input()
	*/
	public function input($buffer) {
		// TODO Auto-generated method stub
		if (strlen($buffer) >= $this->_maxLength) {
			return -1;
		}
		//先找\n再找\r
		$eol = strpos($buffer, $this->_delimiter);
		if ($eol === false) {
			return 0;
		}
		//返回当前包长
		return $eol + strlen($this->_delimiter);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::encode()
	 */
	public function encode($buffer) {
	
		return $buffer . $this->_delimiter;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::decode()
	 */
	public function decode($buffer) {
		//不保留相应的分隔符
		return substr($buffer, 0,strpos($buffer, $this->_delimiter));
	}

}
