<?php

class Request {

    private $data;    
    private $server;
    private $headers;
    private $cookies;
    private $files;
    
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @var InstanceManager
     */
    private $im;
    
    const ONE_YEAR_SECONDS = 31536000;

    public function __construct() {
        $this->im = InstanceManager::getInstance();
        $this->data = $_REQUEST;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->headers = getallheaders();
        $this->config = $this->im->get('config');
        $this->processJsonData();
    }
    
    private function processJsonData() {
        if ($this->getHeader('Content-Type') != 'application/json') {
            return;
        }
        $json = file_get_contents('php://input');
        if (!$json) {
            return;
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
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

    public function getHeader($name, $defaultValue=null) {
        return isset($this->headers[$name]) ? $this->headers[$name] : $defaultValue;
    }
    
    public function getServer($name, $defaultValue=null) {
        return isset($this->server[$name]) ? $this->server[$name] : $defaultValue;
    }
    
    public function getCookie($name, $defaultValue=null) {
        return isset($this->cookies[$name]) ? $this->cookies[$name] : $defaultValue;
    }

    public function setCookie($name, $value, $time=null) {
        $this->cookies[$name] = $value;
        setcookie($name, $value, $time ? $time : time() + self::ONE_YEAR_SECONDS);
    }

    public function isPost() {
        return $this->getServer('REQUEST_METHOD') == 'POST';
    }

    public function getIp() {
        if (!empty($this->getServer('HTTP_CLIENT_IP'))) {
            $ip = $this->getServer('HTTP_CLIENT_IP');
        } else if (!empty($this->getServer('HTTP_X_FORWARDED_FOR'))){
            $ip = $this->getServer('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = $this->getServer('REMOTE_ADDR');
        }
        return $ip;
    }

    public function getDefaultLocale() {
        $acceptLanguage = $this->getServer('HTTP_ACCEPT_LANGUAGE');
        if ($this->config->get('router.use_locale') && $acceptLanguage) {
            $translation = $this->im->get('translation');
            $locales = $translation->getAllLocales();
            $acceptLocale = mb_strtolower(mb_substr($acceptLanguage, 0, 2));
            if (in_array($acceptLocale, $locales)) {
                return $acceptLocale;
            }
        }
        return $this->config->get('translation.default', 'en');
    }
    
    public function getFileTempPath($name) {
        return isset($this->files[$name]) ? $this->files[$name]['tmp_name'] : '';
    }
    
    public function getFileSize($name) {
        return isset($this->files[$name]) ? $this->files[$name]['size'] : -1;
    }

    public function getFileName($name) {
        return isset($this->files[$name]) ? $this->files[$name]['name'] : '';
    }
    
    public function getFileError($name) {
        return isset($this->files[$name]) ? $this->files[$name]['error'] : null;
    }
}
