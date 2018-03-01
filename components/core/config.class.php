<?php

class Config {

    private $attributes = [];

    public function __construct($im) {}

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
        $iniData = parse_ini_file($path);
        $data = [];
        if ($iniData) {            
            if (isset($data['all'])) {
                $data = array_merge($data, $iniData['all']);
            }
            if (isset($data[$group])) {
                $data = array_merge($data, $iniData[$group]);
            }
        }
        $this->attributes = $data;
    }

}