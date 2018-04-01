<?php

class ColumnView {

    protected $columnName;
    protected $label;

    public function __construct($columnName, $label) {
        $this->columnName = $columnName;
        $this->label = $label;
    }

    public function getLabel() {
        return $this->label;
    }

    public function fetch(Record $record) {
        return htmlspecialchars($record->get($this->columnName));
    }

}