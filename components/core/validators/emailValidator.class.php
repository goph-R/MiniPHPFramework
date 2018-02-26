<?php

class EmailValidator extends Validator {

    public function __construct() {
        $this->error = "{label} isn't valid";
    }

    public function doValidate($value) {
        // TODO: for international email addresses
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

}