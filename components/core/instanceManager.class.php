<?php

class InstanceManager {

    private $data = [];

    public function add($name, $object) {
        $this->data[$name] = $object;
    }

    public function init() {
        foreach ($this->data as $name => $instance) {
            if (method_exists($instance, 'init')) {
                $instance->init();
            }
        }
    }

    public function get($name) {
        if (!isset($this->data[$name])) {
            throw new Exception("Instance not exists: ".$name);
        }
        return $this->data[$name];
    }

    public function getAll() {
        return $this->data;
    }

}