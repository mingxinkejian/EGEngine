<?php

namespace Extension\DataFrame;
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
	
	/**
	 * (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::decode()
	 */
	public function decode(&$inData, &$outData) {
		
	}
	
}
