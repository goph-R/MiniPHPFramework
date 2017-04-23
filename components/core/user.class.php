<?php

class User {

	private $request;
	private $config;
	private $id;
	private $name;
	private $email;

	public function __construct($config, $request) {
		session_start();
		$this->request = $request;
		$this->config = $config;
	}

	public function get($name, $defaultValue = null) {
		return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : $defaultValue;
	}

	public function set($name, $value) {
		$_SESSION[$name] = $value;
	}

	private function getHash() {
		return md5($this->request->getHeader('User-Agent').$this->request->getIp());
	}

	public function setLoggedIn($in) {
		$this->set('user.hash', $in ? $this->getHash() : '');
	}

	public function isLoggedIn() {
		return $this->get('user.hash') == $this->getHash();
	}

	public function destroy() {
		session_destroy();
	}
}
