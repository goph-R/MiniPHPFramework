<?php

class Translation {

    /**
     * @var Request
     */
    private $request;
    private $paths;
    private $data;
    private $defaultLocale;
    private $allLocales;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->request = $im->get('request');
        $config = $im->get('config');
        $this->defaultLocale = $config->get('translation.default');
        $all = $config->get('translation.all', $this->defaultLocale);
        $this->allLocales = explode(',', $all);
    }

    public function add($namespace, $path) {
        $this->data[$namespace] = false;
        $this->paths[$namespace] = $path;
    }
    
    public function getAllLocales() {
        return $this->allLocales;
    }
    
    public function getDefaultLocale() {
        return $this->defaultLocale;
    }

    public function get($namespace, $name, $params=[]) {
        $result = '#'.$namespace.'.'.$name.'#';
        if (!isset($this->paths[$namespace]) || !isset($this->data[$namespace])) {
            return $result;
        }
        if ($this->data[$namespace] === false) {
            $locale = $this->request->get('locale');
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