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
	
	/**
	 * 检查完整性
	 * @param unknown $buffer
	 */
	public abstract function input($buffer);

	/**
	 * 打包数据
	 * @param unknown $buffer
	 */
	public abstract function encode($buffer);
	/**
	 * 解析数据包
	 * @param unknown $inData
	 * @param unknown $outData
	 */
	public abstract function decode($buffer);
}
