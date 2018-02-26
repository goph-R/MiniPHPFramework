<?php 

abstract class Validator {

    protected $error = '';
    protected $replacedError = '';

    public function validate($label, $value) {
        $this->replacedError = str_replace('{label}', $label, $this->error);
        return $this->doValidate($value);
    }

    public function getError() {
        return $this->replacedError;
    }

    abstract function doValidate($value);

}