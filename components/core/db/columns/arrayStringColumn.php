<?php

class ArrayStringColumn extends Column {

    private $maxLength = 255;

    public function __construct($name, $maxLength=255) {
        parent::__construct($name);
        $this->maxLength = $maxLength;
    }
    
    public function convertFrom($value) {
        if (!$value) {
            return null;
        }
        $v = mb_substr($value, 1, -1);
        return explode(',', $v);
    }
    
    public function convertTo($value) {
        if (!$value) {
            return null;
        }
        return ','.join(',', $value).',';
    }
}
