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
            $column = new Column(null, $name);
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
        $result = [];
        foreach (array_keys($this->attributes) as $name) {
            $result[$name] = $this->get($name);
        }
        return $result;
    }

    public function set($name, $value=null, $modified=true) {
        if (is_array($name)) {
            foreach ($name as $n => $value) {
                $this->set($n, $value);
            }            
        } else {
            $column = $this->getColumn($name);
            $this->attributes[$name] = $column->convertTo($value);
            if ($modified && !in_array($name, $this->modified)) {
                $this->modified[] = $name;
            }
        }
    }

    public function setRaw($name, $value) {
        $this->getColumn($name);
        $this->attributes[$name] = $value;
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
