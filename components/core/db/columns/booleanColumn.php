<?php

class BooleanColumn extends Column {

    public function convertFrom($value) {
        return $value ? 1 : 0;
    }
    
    public function convertTo($value) {
        return $value ? 1 : 0;
    }    
}
