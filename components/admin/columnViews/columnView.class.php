<?php

class ColumnView {

    protected $columnName;
    protected $label;
    protected $width;
    protected $align;

    public function __construct($columnName, $label, $align='left', $width=null) {
        $this->columnName = $columnName;
        $this->label = $label;
        $this->width = $width;
        $this->align = $align;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getAlign() {
        return $this->align;
    }

    public function fetch(Record $record) {
        return htmlspecialchars($record->get($this->columnName));
    }

}