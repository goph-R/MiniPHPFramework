<?php

class LoginForm extends Form {

    public function __construct() {
        parent::__construct();
        $this->addInput('Email', new TextInput('email'));
        $this->addInput(['user', 'password'], new PasswordInput('password'));
        $checkbox = new CheckboxInput('remember', '1', ['user', 'remember_me']);
        $checkbox->setRequired(false);
        $this->addInput('', $checkbox);
    }

}
