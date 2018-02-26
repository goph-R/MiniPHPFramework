<?php

class LoginForm extends Form {

    public function create() {
        $notEmptyValidator = new NotEmptyValidator();
        $this->addInput('Email', new TextInput($this->im, 'email'));
        $this->addValidator('email', $notEmptyValidator);
        $password = new PasswordInput($this->im, 'password');
        $password->setTrimValue(false);
        $this->addInput('Password', $password);
        $this->addValidator('password', $notEmptyValidator);
    }

}