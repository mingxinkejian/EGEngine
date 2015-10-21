<?php

namespace Core;

use Exception\EGException;

trait EGObjectTrait {
	public function __construct($config = []) {
		foreach ( $config as $name => $value ) {
			$this->$name = $value;
		}
		
		$this->init ();
	}
	public function init() {
	}
	public function __set($name, $value) {
		$setter = 'set' . $name;
		if (method_exists ( $this, $setter )) {
			$this->$setter ( $value );
		} elseif (method_exists ( $this, 'get' . $name )) {
			throw new EGException ( 'Setting read-only property: ' . get_class ( $this ) . '::' . $name );
		} else {
			throw new EGException ( 'Setting unknown property: ' . get_class ( $this ) . '::' . $name );
		}
	}
	public function __get($name) {
		$getter = 'get' . $name;
		if (method_exists ( $this, $getter )) {
			return $this->$getter ();
		} elseif (method_exists ( $this, 'set' . $name )) {
			throw new EGException ( 'Getting write-only property: ' . get_class ( $this ) . '::' . $name );
		} else {
			throw new EGException ( 'Getting unknown property: ' . get_class ( $this ) . '::' . $name );
		}
	}
	public function __isset($name) {
		$getter = 'get' . $name;
		if (method_exists ( $this, $getter )) {
			return $this->$getter () !== null;
		} else {
			return false;
		}
	}
	public function __unset($name) {
		$setter = 'set' . $name;
		if (method_exists ( $this, $setter )) {
			$this->$setter ( null );
		} elseif (method_exists ( $this, 'get' . $name )) {
			throw new EGException ( 'Unsetting read-only property: ' . get_class ( $this ) . '::' . $name );
		}
	}
	public function __call($name, $params) {
		throw new EGException ( 'Calling unknown method: ' . get_class ( $this ) . "::$name()" );
	}
}
