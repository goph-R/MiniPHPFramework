<?php

class SameValidator extends Validator {

    private $otherInput;

    public function __construct($otherInput, $otherLabel) {
        $this->error = "{label} need to be the same as ".$otherLabel;
        $this->otherInput = $otherInput;
    }

    public function doValidate($value) {
        if ($this->otherInput->getValue() != $value) {
            return false;
        }
        return true;
    }

}