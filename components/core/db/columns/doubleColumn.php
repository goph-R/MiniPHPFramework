<?php

class DoubleColumn extends Column {
    
    public function convertFrom($value) {
        return (double)$value;
    }

    public function convertTo($value) {
        return (double)$value;
    }
}