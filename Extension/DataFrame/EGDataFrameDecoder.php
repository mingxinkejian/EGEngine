<?php

namespace Extension\DataFrame;
/**
 * 
 * |Do the most simple game's server framework
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |author mintingjian Date:2016-12-23 Time:下午4:32:47
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |	email:mingtingjian@sina.com                          
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Copyright (c) 2015 EasyGameEngine
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Desc:
 * 用于处理网络层的原始数据，处理拆包粘包等
 */
abstract class EGDataFrameDecoder {
	
	

	protected function callDecode(&$inData,&$outData) {
	
	}
	/**
	 * 解析数据包
	 * @param unknown $inData
	 * @param unknown $outData
	 */
	public function decode(&$inData,&$outData);

}
