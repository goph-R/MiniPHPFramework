<?php

class InstanceManager {

    private static $instance = null;

    /**
     * @return InstanceManager
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new InstanceManager();
        }
        return self::$instance;
    }

    private $data = [];
    private $order = [];

    private function __construct() {}

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
            if ($instance instanceof Initiable) {
                $instance->init();
            }
        }
    }

    public function finish() {
        foreach ($this->order as $name) {
            $instance = $this->data[$name];
            if ($instance instanceof Finishable) {
                $instance->finish();
            }
        }
    }

    public function get($name) {
        if (!isset($this->data[$name])) {
            throw new Exception("Instance not exists: ".$name);
        }
        if (is_string($this->data[$name])) {
            $className = $this->data[$name];
            $instance = new $className();
            if ($instance instanceof Initiable) {
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