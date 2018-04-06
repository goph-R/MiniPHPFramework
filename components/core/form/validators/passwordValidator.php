<?php

class PasswordValidator extends Validator {

    public function __construct() {
        parent::__construct();
        $this->error = $this->translation->get('core', 'not_valid_password');
    }

    public function doValidate($value) {
        if (!$value) {
            return false;
        }        
        return true;
    }

}