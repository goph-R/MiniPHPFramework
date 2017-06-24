<?php

class Config {
	
	private $attributes = [];

	public function __construct($im) {}
	
	public function set($name, $value) {
		$this->attributes[$name] = $value;
	}
	
	public function get($name, $defaultValue = null) {		
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
		return $defaultValue;
	}

}