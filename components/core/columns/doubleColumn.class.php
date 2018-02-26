<?php

class DoubleColumn extends Column {
    public function convert($value) {
        return (double)$value;
    }
}