<?php

class EmailValidator extends Validator {

    public function __construct($im) {
        parent::__construct($im);
        $this->error = $this->translation->get('core', 'not_valid_email');
    }

    public function doValidate($value) {
        // TODO: for international email addresses
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

}