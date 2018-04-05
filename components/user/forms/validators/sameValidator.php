<?php

class SameValidator extends Validator {

    private $otherInput;

    public function __construct(Input $otherInput) {
        parent::__construct();
        $this->error = $this->translation->get('user', 'didnt_match');
        $this->otherInput = $otherInput;
    }

    public function doValidate($value) {
        if ($this->otherInput->getValue() != $value) {
            return false;
        }
        return true;
    }

}