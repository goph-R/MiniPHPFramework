<?php

class User {

	private $request;
	private $config;

	public function __construct($im) {
		session_start();
		$this->request = $im->get('request');
		$this->config = $im->get('config');
	}

    public function get($name, $defaultValue = null) {
		return isset($_SESSION[$name]) ? $_SESSION[$name] : $defaultValue;
	}

	public function set($name, $value) {
		$_SESSION[$name] = $value;
	}

	public function getHash() {
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

	public function setFlash($message) {
	    $this->set('user.flash', $message);
    }

    public function hasFlash() {
        return $this->get('user.flash', '') ? true : false;
    }

    public function getFlash() {
        $result = $this->get('user.flash', '');
        $this->set('user.flash', '');
        return $result;
    }

}
