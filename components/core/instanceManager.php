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
    
    public function initComponents($componentNames) {
        foreach ($componentNames as $componentName) {
            require_once "components/$componentName/init.php";
        }
    }
    
    public function finish() {
        foreach (array_reverse($this->order) as $name) {
            $instance = $this->data[$name];
            if ($instance instanceof Finishable) {
                $instance->finish();
            }
        }
    }

    public function get($name, $args=[]) {
        if (!isset($this->data[$name])) {
            throw new RuntimeException("Instance not exists: ".$name);
        }
        if (is_string($this->data[$name])) {
            $className = $this->data[$name];
            $instance = $this->create($className, $args);
            $this->data[$name] = $instance;
        }
        return $this->data[$name];
    }
    
    public function create($className, $args) {
        $reflect = new ReflectionClass($className);
        $instance = $reflect->newInstanceArgs($args);
        return $instance;
    }    

    public function getAll() {
        return $this->data;
    }

}