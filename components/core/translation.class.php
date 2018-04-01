<?php

class Translation {

    /**
     * @var Request
     */
    private $request;
    private $paths;
    private $data;
    private $default;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->request = $im->get('request');
        $this->default = $im->get('config')->get('translation.default', 'en');
    }

    public function add($namespace, $path) {
        $this->data[$namespace] = false;
        $this->paths[$namespace] = $path;
    }

    public function get($namespace, $name, $params=[]) {
        $result = '#'.$namespace.'.'.$name.'#';
        if (!isset($this->paths[$namespace]) || !isset($this->data[$namespace])) {
            return $result;
        }
        if ($this->data[$namespace] === false) {
            $locale = $this->request->get('locale', $this->default);
            $path = $this->paths[$namespace].'/'.$locale.'.ini';
            $iniData = file_exists($path) ? parse_ini_file($path) : [];
            $this->data[$namespace] = $iniData;
        }
        if (isset($this->data[$namespace][$name])) {
            $result = $this->data[$namespace][$name];
        }
        foreach ($params as $name => $value) {
            $result = str_replace('{'.$name.'}', $value, $result);
        }
        return $result;
    }

}