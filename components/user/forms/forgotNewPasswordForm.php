<?php

class ForgotNewPasswordForm extends Form {

    public function __construct() {
        parent::__construct();
        $password = new PasswordInput('password');
        $passwordAgain = new PasswordInput('password_again');
        $this->addInput(['user', 'password'], $password);
        $this->addInput(['user', 'password_again'], $passwordAgain);
        $this->addValidator('password', new PasswordValidator());
        $this->addValidator('password_again', new SameValidator($this, 'password'));
    }

}