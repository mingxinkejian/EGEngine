<?php

namespace Extension;
/**
 * 
 * |Do the most simple game's server framework
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |author mintingjian Date:2016-7-13 Time:下午5:40:29
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |	email:mingtingjian@sina.com                          
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Copyright (c) 2015 EasyGameEngine
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Desc:用于封装二进制流
 */
class EGSockBuffer {
	//大端
	const SOCKBUFFER_BIG = 1;
	//小端
	const SOCKBUFFER_LITTLE = 2;
	
	private $_datas;
	public $_rPos = 0;
	public $_wPos = 0;
	private$_endian;
	public function __construct($endian = SOCKBUFFER_BIG){
		$this->_endian = $endian;
	}
	
	public function data(){
		return $this->_datas;
	}
	
	public function setData($data){
		$this->_datas = $data;
	}
	
	public function readInt8(){
		$bytes = substr($this->_datas, $this->_rPos,++$this->_rPos);
		if ($this->_endian == SOCKBUFFER_BIG){
			$result = unpack('n', $bytes);
		}else{
			$result = unpack('v', $bytes);
		}
		
		return $result[1];
	}
	
	public function readInt16(){
		$bytes = substr($this->_datas, $this->_rPos,$this->_rPos + 2);
		if ($this->_endian == SOCKBUFFER_BIG){
			$result = unpack('n', $bytes);
		}else{
			$result = unpack('v', $bytes);
		}
		$this->_rPos += 2;
		return $result;
	}
	
	public function readInt32(){
		$bytes = substr($this->_datas, $this->_rPos,$this->_rPos + 4);
		if ($this->_endian == SOCKBUFFER_BIG){
			$result = unpack('N', $bytes);
		}else{
			$result = unpack('V', $bytes);
		}
		$this->_rPos += 4;
		return $result[1];
	}
	
	public function readInt64(){
		
	}
	
	public function readUint8(){
		
	}
	
	public function readUint16(){
	
	}
	
	public function readUint32(){
	
	}
	
	public function readUint64(){
	
	}
	
	public function readFloat(){
		$bytes = substr($this->_datas, $this->_rPos,$this->_rPos + 4);
		if ($this->_endian == SOCKBUFFER_BIG){
			$result = unpack('f', strrev($bytes));
		}else{
			$result = unpack('V', $bytes);
		}
		$this->_rPos += 4;
		return $result[1];
	}
	
	public function readDouble(){
	
	}
	
	public function readString(){
	
	}
	
	public function writeInt8($value){
		
	}
	
	public function writeInt16($value){
	
	}
	
	public function writeInt32($value){
	
	}
	
	public function writeInt64($value){
	
	}
	
	public function writeUint8($value){
	
	}
	
	public function writeUint16($value){
	
	}
	
	public function writeUint32($value){
	
	}
	
	public function writeUint64($value){
	
	}
	
	public function writeFloat($value){
	
	}
	
	public function writeDouble($value){
	
	}
	
	public function writeString($value){
	
	}
	
	public function length(){
		return $this->_wPos - $this->_rPos;
	}
}
