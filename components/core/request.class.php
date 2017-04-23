<?php

class Request {

	public function get($name, $defaultValue = null) {
		if (isset($_REQUEST[$name])) {
			return $_REQUEST[$name];
		}
		return $defaultValue;
	}

	public function getHeader($name, $defaultValue = null) {
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}
		return $defaultValue;
	}

	public function isPost() {
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	public function getIp() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}		
		return $ip;
	}	

}
