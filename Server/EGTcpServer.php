<?php

namespace Server;

class EGTcpServer extends EGSocketServer{
	
	public function __construct($host, $port) {
		if (! $host || ! $port ) {
			echo "please confirm the params !\n";
			exit ();
		}
		$this->_defaultHost = $host;
		$this->_defaultPort = $port;
		$this->_server = new \swoole_server( $host, $port );
	}
}
