<?php

class InstanceManager {

    private $data = [];
    private $order = [];

    public function add($name, $object) {
        $this->data[$name] = $object;
        $this->order[] = $name;
    }

    public function init() {
        $this->get('translation')->add('core', 'components/core/translations');
        foreach ($this->order as $name) {
            $instance = $this->data[$name];
            if (method_exists($instance, 'init')) { // TODO: "Initiable" interface maybe?
                $instance->init();
            }
        }
    }

    public function done() {
        foreach ($this->order as $name) {
            $instance = $this->data[$name];
            if (method_exists($instance, 'done')) { // TODO: "Doneable" interface maybe?
                $instance->done();
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