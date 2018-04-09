<?php

class Config {

    private $attributes = [];
    private $environment;

    public function __construct($path, $environment) {
        $this->load($path, $environment);
        $this->environment = $environment;
    }

    public function getEnvironment() {
        return $this->environment;
    }

    public function set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function get($name, $defaultValue = null) {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return $defaultValue;
    }

    public function load($path, $group) {
        $iniData = parse_ini_file($path, true, INI_SCANNER_TYPED);
        $data = [];
        if ($iniData) {            
            if (isset($iniData['all'])) {
                $data = array_merge($data, $iniData['all']);
            }
            if (isset($iniData[$group])) {
                $data = array_merge($data, $iniData[$group]);
            }
        }
        $this->attributes = $data;
    }

}