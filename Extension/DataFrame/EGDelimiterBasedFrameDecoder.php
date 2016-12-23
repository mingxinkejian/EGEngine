<?php

namespace Extension\DataFrame;
/**
 * 
 * |Do the most simple game's server framework
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |author mintingjian Date:2016-12-23 Time:下午5:11:45
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |	email:mingtingjian@sina.com                          
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Copyright (c) 2015 EasyGameEngine
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Desc:
 * 基于界定符解码器
 * 原理如下：
 * 假设收到的报文如下：
 * +--------------------+
 * | ABC\nDEF\r\n |
 * +--------------------+
 * 如果以‘\n’为界定符，则拆包粘包后的报文就是：
 * +--------+-------+
 * | ABC | DEF |
 * +--------+-------+
 * 如果以‘\r\n’为界定符，则拆包粘包后的报文就是：
 * +-----------------+
 * | ABC\nDEF |
 * +-----------------+
 */
class EGDelimiterBasedFrameDecoder extends EGDataFrameDecoder{

	/**
	 * (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::decode()
	 */
	public function decode(&$inData, &$outData) {
	
	}
}
