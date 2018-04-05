<?php

class Request {

    private $data;
    private $headers;

    const ONE_YEAR_SECONDS = 31536000;

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

    public function getHeader($name, $defaultValue=null) {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
        return $defaultValue;
    }

    public function getCookie($name, $defaultValue=null) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $defaultValue;
    }

    public function setCookie($name, $value, $time=null) {
        setcookie($name, $value, $time ? $time : time() + self::ONE_YEAR_SECONDS);
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

    public function getDefaultLocale() {
        if (isset($this->headers['HTTP_ACCEPT_LANGUAGE'])) {
            return substr($this->headers['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
        return 'en';
    }

}
