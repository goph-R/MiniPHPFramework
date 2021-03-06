<?php

class User {

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    private $permissions;

    public function __construct() {
        session_start();
        $im = InstanceManager::getInstance();
        $this->request = $im->get('request');
        $this->config = $im->get('config');
        $this->permissions = $this->get('permissions', []);
    }

    public function get($name, $defaultValue=null) {
        $key = 'user.'.$name;
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    public function set($name, $value) {
        $key = 'user.'.$name;
        $_SESSION[$key] = $value;
    }

    public function getHash() {
        return md5($this->request->getHeader('User-Agent').$this->request->getIp());
    }

    public function setLoggedIn($in) {
        $this->set('hash', $in ? $this->getHash() : '');
    }

    public function isLoggedIn() {
        return $this->get('hash') == $this->getHash();
    }

    public function destroy() {
        session_destroy();
    }

    public function setFlash($name, $message) {
        $this->set('flash.'.$name, $message);
    }

    public function hasFlash($name) {
        return $this->get('flash.'.$name, '') ? true : false;
    }

    public function getFlash($name) {
        $result = $this->get('flash.'.$name, '');
        $this->set('flash.'.$name, null);
        return $result;
    }

    public function addPermission($name) {
        $this->permissions[] = $name;
        $this->set('permissions', $this->permissions);
    }

    public function hasPermission($name) {
        return in_array($name, $this->permissions);
    }    

}
