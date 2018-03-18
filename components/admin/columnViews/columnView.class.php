<?php

class ColumnView {

    protected $columnName;
    protected $label;
    protected $im;

    public function __construct($im, $columnName, $label) {
        $this->im = $im;
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