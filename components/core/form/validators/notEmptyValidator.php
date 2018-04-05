<?php

class NotEmptyValidator extends Validator {

    public function __construct() {
        parent::__construct();
        $this->error = $this->translation->get('core', 'cant_be_empty');
    }

    public function doValidate($value) {
        if (!$value) {
            return false;
        }
        return true;
    }

}