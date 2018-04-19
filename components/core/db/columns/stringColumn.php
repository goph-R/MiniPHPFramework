<?php

class StringColumn extends Column {

    private $maxLength;

    public function __construct($name, $maxLength=255) {
        parent::__construct($name);
        $this->maxLength = $maxLength;
    }

    public function getMaxLength() {
        return $this->maxLength;
    }
}
