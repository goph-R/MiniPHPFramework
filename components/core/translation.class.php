<?php

class Translation {

    private $paths;
    private $data;
    private $request;

    public function __construct($im) {
        $this->request = $im->get('request');
    }

    public function add($namespace, $path) {
        $this->data[$namespace] = false;
        $this->paths[$namespace] = $path;
    }

    public function get($name, $params=[], $namespace='default') {
        $result = '#'.$name.'#';
        if (!isset($this->paths[$namespace]) || !isset($this->data[$namespace])) {
            return $result;
        }
        if ($this->data[$namespace] === false) {
            $locale = $this->request->get('locale', 'en'); // TODO: default locale to config
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