<?php

class Record {

    /**
     * @var Table
     */
    private $table;

    private $new = true;
    private $modified = [];
    private $attributes = [];

    public function __construct(Table $table) {
        $this->table = $table;
    }

    /**
     * @return Table
     */
    public function getTable() {
        return $this->table;
    }

    public function isNew() {
        return $this->new;
    }

    public function setNew($new) {
        $this->new = $new;
    }
    
    private function getColumn($name) {
        $column = $this->table->getColumn($name);
        if (!$column) {
            throw new DBException('Try to get a non existing column: '.$name);
        }    
        return $column;
    }

    public function get($name) {
        $column = $this->getColumn($name);
        if (!isset($this->attributes[$name])) {
            return $column->getDefaultValue();
        }
        return $column->convertFrom($this->attributes[$name]);
    }
    
    public function getRaw($name) {
        $column = $this->getColumn($name);
        if (!isset($this->attributes[$name])) {
            return $column->getDefaultValue();
        }        
        return $this->attributes[$name];
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function set($name, $value, $modified = true) {
        $column = $this->table->getColumn($name);
        if (!$column) {
            throw new DBException('Try to modify a non existing column: '.$name);
        }
        $this->attributes[$name] = $column->convertTo($value);
        if ($modified && !in_array($name, $this->modified)) {
            $this->modified[] = $name;
        }
    }

    public function clearModified() {
        $this->modified = [];
    }

    public function getModified() {
        return $this->modified;
    }

    public function save() {
        $this->table->save($this);
    }

    public function delete() {
        $this->table->delete($this);
    }

    public function setAll($fields, $values) {
        foreach ($fields as $field) {
            if (isset($values[$field])) {
                $this->set($field, $values[$field]);
            }
        }
    }

}
