<?php

namespace GateWayWorker;
use Server\EGTcpServer;
/**
 * 
 * |Do the most simple game's server framework
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |author mintingjian Date:2016-12-19 Time:下午6:25:36
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |	email:mingtingjian@sina.com                          
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Copyright (c) 2015 EasyGameEngine
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * |++++++++++++++++++++++++++++++++++++++++++++++++++
 * |Desc:
 * 注册中心，用于注册Gateway和worker
 */
class EGGateWayRegister extends EGTcpServer{
	
	
	public function __construct($host, $port) {
		parent::__construct($host, $port);
		
		$this->_server->on('connect',array($this,'onConnect'));
	}
}
