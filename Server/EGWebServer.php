<?php

namespace Server;

class EGWebServer extends EGBaseServer{
	const SEVERNAME ='EGEServer';
	
	protected $_webRoot;
	
	
	public function setWebRoot($webRoot){
		$this->_webRoot=$webRoot;
	}
}
