<?php

namespace Extension\DataFrame;
/**
 * 
 * |Do the most simple game's server framework
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |author mintingjian Date:2016-12-23 Time:下午5:12:23
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |	email:mingtingjian@sina.com                          
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Copyright (c) 2015 EasyGameEngine
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Desc:
 * 基于长度字段解码器
 * 所谓长字段就是在报文里有说明报文总长度的字段，
 * 其实在TCP的报文规则里就用的这个方法，在头部存放报文总长或除报头的内容总长
 * 
 * 长度包含长度字段本身且不排除本身的拆包与粘包：
 * lengthFieldOffset   = 0     长度字段偏移量
 * lengthFieldLength   = 2    长度字段所占长度
 * lengthAdjustment    = 0    
 * initialBytesToStrip = 0      (要排除的用于初始化的偏移位置)
 * 
 *          解码前 (14 bytes)                                    解码后 (14 bytes)
 * +------------+---------------------------+             +------------+---------------------------+
 * | Length  | Actual Content |   				----->    | Length | Actual Content |
 * | 0x000C  | "HELLO, WORLD" |        				      | 0x000C | "HELLO, WORLD" |
 * +------------+----------------------------+            +-----------+----------------------------+
 * 长度包含长度字段本身且排除本身的拆包与粘包：
 * 
 * lengthFieldOffset   = 0     长度字段偏移量
 * lengthFieldLength   = 2     长度字段所占长度
 * lengthAdjustment    = 0
 * initialBytesToStrip = 2 (排除头部)
 *             解码前 (14 bytes)                        解码后 (12 bytes)
 * +------------+----------------------------+           +---------------------------+
 * | Length  | Actual Content |     			----->	 | Actual Content |
 * | 0x000C  | "HELLO, WORLD" |           				 | "HELLO, WORLD" |
 * +------------+----------------------------+           +----------------------------+
 * 长度包含长度字段本身且不排除本身的拆包与粘包：
 * 
 * lengthFieldOffset   =  0   长度字段偏移量
 * lengthFieldLength   =  2   长度字段偏移量
 * lengthAdjustment    = -2  调整长度 (长度字段所占长度)
 * initialBytesToStrip =  0
 *       解码前 (14 bytes)                                   解码后 (14 bytes)
 * +------------+----------------------------+             +-----------+----------------------------+
 * | Length  | Actual Content |   				----->     | Length  | Actual Content |
 * | 0x000E  | "HELLO, WORLD" |         			       | 0x000E  | "HELLO, WORLD" |
 * +------------+----------------------------+             +-----------+----------------------------+
 * 有外部头部的拆包与粘包：
 * 
 * lengthFieldOffset   = 2        长度字段偏移量 ( = 外部头部Header 1的长度)
 * lengthFieldLength   = 3      长度字段占用字节数
 * lengthAdjustment    = 0
 * initialBytesToStrip = 0
 *                 解码前 (17 bytes)                                                       解码后 (17 bytes)
 * +--------------+--------------+--------------------------+              +-------------+---------------+--------------------------+
 * | Header 1 | Length   | Actual Content |    					----->     | Header 1 | Length   | Actual Content |
 * | 0xCAFE   | 0x00000C | "HELLO, WORLD" |             				   | 0xCAFE   | 0x00000C | "HELLO, WORLD" |
 * +--------------+--------------+--------------------------+              +--------------+--------------+--------------------------+
 * 长度字段在前且有扩展头部的拆包与粘包：
 * 
 * lengthFieldOffset   = 0    长度字段偏移量
 * lengthFieldLength   = 3   长度字段占用字节数
 * lengthAdjustment    = 2 ( Header 1 的长度)
 * initialBytesToStrip = 0
 *                 	解码前 (17 bytes)                                         				   解码后 (17 bytes)
 * +--------------+--------------+--------------------------+              +--------------+--------------+--------------------------+
 * | Length   |  Header 1 | Actual Content |    				----->     | Length   | Header 1 | Actual Content |
 * | 0x00000C |  0xCAFE   | "HELLO, WORLD" |               				   | 0x00000C | 0xCAFE   | "HELLO, WORLD" |
 * +--------------+--------------+--------------------------+              +--------------+--------------+--------------------------+
 * 多扩展头部的拆包与粘包：
 * 
 * lengthFieldOffset   = 1    长度字段偏移量(=头HDR1的长度)
 * lengthFieldLength   = 2   长度字段占用字节数
 * lengthAdjustment    = 1  调整长度(= 头HDR2的长度)
 * initialBytesToStrip = 3     排除的偏移量(= the length of HDR1 + LEN)
 *                       解码前 (16 bytes)                                           解码后 (13 bytes)
 * +--------------+--------------+--------------------------+              +--------------+--------------+--------------------------+
 * | HDR1 | Length | HDR2 | Actual Content |    				 ----->    | HDR2 | Actual Content |
 * | 0xCA | 0x000C | 0xFE | "HELLO, WORLD" |              				   | 0xFE | "HELLO, WORLD" |
 * +--------------+--------------+--------------------------+              +--------------+--------------+--------------------------+
 * 调整的多扩展头部的拆包与粘包：
 * lengthFieldOffset   =  1        长度字段偏移量(=头HDR1的长度)
 * lengthFieldLength   =  2      长度字段占用字节数
 * lengthAdjustment    = -3      (= the length of HDR1 + LEN, negative)
 * initialBytesToStrip =  3        排除的偏移量(= the length of HDR1 + LEN)
 *                    解码前 (16 bytes)                                                        解码后 (13 bytes)
 * +--------------+--------------+--------------------------+              +--------------+--------------+--------------------------+
 * | HDR1 | Length | HDR2 | Actual Content |     				----->     | HDR2 | Actual Content |
 * | 0xCA | 0x0010 | 0xFE | "HELLO, WORLD" |                 			   | 0xFE | "HELLO, WORLD" |
 * +--------------+--------------+--------------------------+              +--------------+--------------+--------------------------+
 */
class EGLengthFieldBasedFrameDecoder extends EGDataFrameDecoder{
	
	private $_byteOrder;
	private $_maxLength;
	private $_lengthFieldOffset;
	private $_lengthFieldLength;
	private $_lengthFieldEndOffset;
	private $_lengthAdjustment;
	private $_initialBytesToStrip;
	private $_tooLongLength;
	private $_bytesToDiscard;
	
	
	public function __construct($byteOrder,$maxLength,$lengthFieldOffset,$lengthFieldLength,$lengthFieldEndOffset,$lengthAdjustment,$initialBytesToStrip) {
		$this->_byteOrder = $byteOrder;
		$this->_maxLength = $maxLength;
		$this->_lengthFieldOffset = $lengthFieldOffset;
		$this->_lengthFieldLength = $lengthFieldLength;
		$this->_lengthFieldEndOffset = $lengthFieldEndOffset;
		$this->_lengthAdjustment = $lengthAdjustment;
		$this->_initialBytesToStrip = $initialBytesToStrip;
	}
	
	/* (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::input()
	*/
	public function input($buffer) {
		// TODO Auto-generated method stub
		//判断第几个字节是长度位置
		$bufLen = strlen($buffer);
		if ($bufLen >= $this->_maxLength) {
			return -1;
		}else if($bufLen < $this->_lengthFieldEndOffset) {
			return 0;
		}
		
		
		return $this->getFrameLength($buffer);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::encode()
	 */
	public function encode($data) {
	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Extension\DataFrame\EGDataFrameDecoder::decode()
	 */
	public function decode($buffer) {
		
	}
	
	public function getFrameLength($buffer) {
		
		switch ($this->_lengthFieldLength) {
			case 1:
				$len = unpack('C', $buffer);
				break;
			case 2:
				$len = unpack('n', $buffer);
				break;
			case 3:
				$len = unpack('C3', $buffer);
				break;
			case 4:
				$len = unpack('N', $buffer);
				break;
		}
		
		return $len;
	}
}
