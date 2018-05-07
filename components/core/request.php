<?php

class Request {

    private $data;    
    private $headers;
    
    /**
     * @var Config
     */
    private $config;
    
    const ONE_YEAR_SECONDS = 31536000;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->data = $_REQUEST;
        $this->headers = $_SERVER;
        $this->config = $im->get('config');
        $this->processJsonData();
    }
    
    private function processJsonData() {
        $json = file_get_contents('php://input');
        if (!$json) {
            return;
        }
        $data = json_decode($json, true);
        if ($data === false) {
            return;
        }
        foreach ($data as $name => $value) {
            $this->set($name, $value);
        }
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
        
        if ($this->config->get('router.use_locale') && isset($this->headers['HTTP_ACCEPT_LANGUAGE'])) {
            return mb_strtolower(mb_substr($this->headers['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        }
        return $this->config->get('translation.default', 'en');
    }
    
    public function getFileTempPath($name) {
        return isset($_FILES[$name]) ? $_FILES[$name]['tmp_name'] : '';
    }
    
    public function getFileSize($name) {
        return isset($_FILES[$name]) ? $_FILES[$name]['size'] : -1;
    }

    public function getFileName($name) {
        return isset($_FILES[$name]) ? $_FILES[$name]['name'] : '';
    }
    
    public function getFileError($name) {
        return isset($_FILES[$name]) ? $_FILES[$name]['error'] : null;
    }
}
