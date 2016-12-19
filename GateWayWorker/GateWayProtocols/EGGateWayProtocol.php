<?php

namespace GateWayWorker\GateWayProtocols;
/**
 * Gateway 与 Worker 间通讯的二进制协议
 * 采用定长包处理
 * struct GatewayProtocol
 * {
 *     unsigned int        packLen,
 *     unsigned char       cmd,//命令字
 *     unsigned int        localIp,
 *     unsigned short      localPort,
 *     unsigned int        clientIp,
 *     unsigned short      clientPort,
 *     unsigned int        connectionId,
 *     unsigned char       flag,
 *     unsigned short      gatewayPort,
 *     unsigned int        extLen,
 *     char[ext_len]       extData,
 *     char[pack_length-HEAD_LEN] body//包体
 * }
 * NCNnNnNCnN
 */
class EGGateWayProtocol {
	//包头长度
	const HEAD_LEN = 28;
	//发送给Worker，gateway有一个新连接
	const CMD_ON_CONNECTION = 1;
	//发送给Worker，客户端有新消息
	const CMD_ON_MESSAGE = 3;
	//发送给Worker上的关闭连接事件
	const CMD_ON_CLOSE = 4;
	//发给gateway向的单个用户发送数据
	const CMD_SEND_TO_ONE = 5;
	//发送给gateway向的所有用户发送数据
	const CMD_SEND_TO_ALL = 6;
	//发送给gateway的踢出用户
	const CMD_KICK = 7;
	//发送给gateway，通知用户更新seesion
	const CMD_UPDATE_SESSION = 9;
	//获取在线状态
	const CMD_GET_ALL_CLIENT_INFO = 10;
	//判断是否在线
	const CMD_IS_ONLINE = 11;
	//clientId 绑定到uId
	const CMD_BIND_UID = 12;
	//解除绑定
	const CMD_UNBIND_UID = 13;
	//向uId发送数据
	const CMD_SEND_TO_UID = 14;
	//根据uId获取绑定的clientId
	const CMD_GET_CLIENT_ID_BY_UID = 15;
	//加入组
	const CMD_JOIN_GROUP = 20;
	//离开组
	const CMD_LEAVE_GROUP = 21;
	//向组成员发消息
	const CMD_SEND_TO_GROUP = 22;
	//获取组成员
	const CMD_GET_CLIENT_INFO_BY_GROUP = 23;
	//获取组成员数
	const CMD_GET_CLIENT_COUNT_BY_GROUP = 24;
	//Worker连接gateway事件
	const CMD_WORKER_CONNECT = 200;
	//心跳
	const CMD_PING = 201;
	//gatewayClient连接gateway事件
	const CMD_GATEWAY_CLIENT_CONNECT = 202;
	//根据clientId获取session
	const CMD_GET_SESSION_BY_CLIENT_ID = 203;
	//发送给gateway，覆盖session
	const CMD_SET_SESSION = 204;
	//包体是标量
	const FLAG_BODY_IS_SCALAR = 0x01;
	//通知gateway在send时不调用协议encode方法，在广播组时提升性能
	const FLAG_NOT_CALL_ENCODE = 0x02;
	
	
	
	public static $empty = array(
		'cmd' => 0,
		'localIp' => 0,
		'localPort' => 0,
		'clientIp' => 0,
		'clientPort' => 0,
		'connectionId' => 0,
		'flag' => 0,
		'gatewayPort' => 0,
		'extData' => '',
		'body' => '',
	);
	
	/**
	 * 返回包长度
	 * @param unknown $buffer
	 * @return number|Ambigous <>
	 */
	public static function input($buffer) {
		if (strlen($buffer) < self::HEAD_LEN) {
			return 0;
		}
		
		$data = unpack("NpackLen", $buffer);
		return $data["packLen"];
	}
	
	public static function encode($data) {
		$flag = (int)is_scalar($data['body']);
		if(!$flag) {
			$data['body'] = serialize($data['body']);
		}
		$data['flag'] |= $flag;
		$extLen = strlen($data['extData']);
		//包长
		$packageLen = self::HEAD_LEN + $extLen + strlen($data['body']);
		
		$encodeData = pack
		('NCNnNnNCnN',
				$packageLen,$data['cmd'],$data['localIp'],$data['localPort'],
				$data['clientIp'],$data['clientPort'],$data['connectionId'],
				$data['flag'],$data['gatewayPort'],$extLen
		) . $data['extData'] . $data['body'];
		
		return $encodeData;
	}
	/**
	 * 从二进制数据转换为数组
	 * @param unknown $buffer
	 * @return Ambigous <string, multitype:, mixed>
	 */
	public static function decode($buffer) {
		$data = unpack("NpackLen/Ccmd/NlocalIp/nlocalPort/NclientIp/nclientPort/NconnectionId/Cflag/ngatewayPort/NextLen", $buffer);
		
		if($data['extLen'] > 0) {
			$data['extData'] = substr($buffer, self::HEAD_LEN,$data['extLen']);
			if($data['flag'] & self::FLAG_BODY_IS_SCALAR) {
				$data['body'] = substr($buffer, self::HEAD_LEN + $data['extLen']);
			}else{
				$data['body'] = unserialize(substr($buffer, self::HEAD_LEN + $data['extLen']));
			}
		}else{
			$data['extData'] = '';
			if($data['flag'] & self::FLAG_BODY_IS_SCALAR) {
				$data['body'] = substr($buffer, self::HEAD_LEN);
			}else{
				$data['body'] = unserialize(substr($buffer, self::HEAD_LEN));
			}
		}
		
		return $data;
	}
}
