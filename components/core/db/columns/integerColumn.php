<?php

class IntegerColumn extends Column {    
    
    public function convertFrom($value) {
        return (int)$value;
    }
    
    public function convertTo($value) {
        return (int)$value;
    }    
}
