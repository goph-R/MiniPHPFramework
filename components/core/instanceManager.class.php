<?php

class InstanceManager {

    private $data = [];
    private $order = [];

    public function add($name, $object) {
        $this->data[$name] = $object;
        if (!in_array($name, $this->order)) {
            $this->order[] = $name;
        }
    }

    public function init() {
        $this->get('translation')->add('core', 'components/core/translations');
        $this->get('view')->addPath('core', 'components/core/templates');
        foreach ($this->order as $name) {
            $instance = $this->data[$name];
            if ($instance instanceof Initable) {
                $instance->init();
            }
        }
    }

    public function done() {
        foreach ($this->order as $name) {
            $instance = $this->data[$name];
            if ($instance instanceof Doneable) {
                $instance->done();
            }
        }
    }

    public function get($name) {
        if (!isset($this->data[$name])) {
            throw new Exception("Instance not exists: ".$name);
        }
        if (is_string($this->data[$name])) {
            $className = $this->data[$name];
            $instance = new $className($this);
            if ($instance instanceof Initable) {
                $instance->init();
            }
            $this->data[$name] = $instance;
        }
        return $this->data[$name];
    }

    public function getAll() {
        return $this->data;
    }

}