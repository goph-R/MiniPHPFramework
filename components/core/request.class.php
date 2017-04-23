<?php

class Request {

	private $data;
	private $headers;

	public function __construct() {
		$this->data = $_REQUEST;
		$this->headers = $_SERVER;
	}

	public function get($name, $defaultValue = null) {
		if (isset($this->data[$name])) {
			return $this->data[$name];
		}
		return $defaultValue;
	}

	public function set($name, $value) {
		$this->data[$name] = $value;
	}

	public function setHeader($name, $value) {
		$this->headers[$name] = $value;
	}

	public function getHeader($name, $defaultValue = null) {
		if (isset($this->headers[$name])) {
			return $this->headers[$name];
		}
		return $defaultValue;
	}

	public function isPost() {
		return $this->headers['REQUEST_METHOD'] == 'POST';
	}

	public function getIp() {
		if (!empty($this->headers['HTTP_CLIENT_IP'])) {
		    $ip = $this->headers['HTTP_CLIENT_IP'];
		} else if (!empty($this->headers['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $this->headers['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $this->headers['REMOTE_ADDR'];
		}		
		return $ip;
	}	

}
