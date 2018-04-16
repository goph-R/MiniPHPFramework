<?php

class NotEmptyValidator extends Validator {

    public function __construct() {
        parent::__construct();
        $this->message = $this->translation->get('core', 'cant_be_empty');
    }

    public function doValidate($value) {
        return !empty($value);
    }

}