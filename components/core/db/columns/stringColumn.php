<?php

class StringColumn extends Column {

    private $maxLength = 255;

    public function __construct($table, $name, $maxLength = 255) {
        parent::__construct($table, $name);
        $this->maxLength = $maxLength;
    }
}
