<?php

class LoginForm extends Form {

    public function create() {
        $t = $this->im->get('translation');
        $notEmptyValidator = new NotEmptyValidator($this->im);
        $this->addInput('Email', new TextInput($this->im, 'email'));
        $this->addValidator('email', $notEmptyValidator);
        $password = new PasswordInput($this->im, 'password');
        $password->setTrimValue(false);
        $this->addInput($t->get('user', 'password'), $password);
        $this->addValidator('password', $notEmptyValidator);
    }

}