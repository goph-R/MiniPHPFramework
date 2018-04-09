<?php 

class HiddenInput extends TextInput {

    public function __construct($name, $defaultValue = '') {
        parent::__construct($name, $defaultValue);
        $this->type = 'hidden';
    }
    
}
