<?php

class Column {

    protected $name;

    /**
     * @var Table
     */
    protected $table;
    protected $autoIncrement;
    protected $defaultValue = null;
    // TODO: current_timestamp

    public function __construct($table, $name) {
        $this->table = $table;
        $this->name = $name;
    }

    public function setAutoIncrement($autoIncrement) {
        $this->autoIncrement = $autoIncrement;
    }

    public function setDefaultValue($defaultValue) {
        $this->defaultValue = $defaultValue;
    }

    public function getName() {
        return $this->name;
    }

    public function isAutoIncrement() {
        return $this->autoIncrement;
    }

    public function convertTo($value) {
        return $value;
    }
    
    public function convertFrom($value) {
        return $value;
    }

    public function getDefaultValue() {
        return $this->defaultValue;
    }

}
