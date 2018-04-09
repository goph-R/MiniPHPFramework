<?php

class RegisterForm extends Form {

    public function __construct() {
        parent::__construct();
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator();
        $this->addInput('Email', new TextInput('email'));
        $this->addValidator('email', $notEmptyValidator);
        $this->addValidator('email', new EmailValidator());
        $this->addValidator('email', new EmailExistsValidator());
        $password = new PasswordInput('password');
        $passwordAgain = new PasswordInput('password_again');
        $this->addInput($t->get('user', 'password'), $password);
        $this->addInput($t->get('user', 'password_again'), $passwordAgain);
        $this->addValidator('password', new PasswordValidator());
        $this->addValidator('password_again', new SameValidator($password));
    }

}