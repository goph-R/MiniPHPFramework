<?php

class MustAcceptValidator extends Validator {

    public function __construct() {
        parent::__construct();
        $this->message = $this->translation->get('core', 'must_accept');
    }

    public function doValidate($value) {
        if (!$value) {
            return false;
        }
        return true;
    }

}