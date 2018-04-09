<?php

class PasswordInput extends TextInput {
    
    public function __construct($name, $defaultValue='') {
        parent::__construct($name, $defaultValue);
        $this->type = 'password';
        $this->trimValue = false;
    }
    
}