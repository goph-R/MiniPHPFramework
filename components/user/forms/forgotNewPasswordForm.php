<?php

class ForgotNewPasswordForm extends Form {

    public function __construct() {
        parent::__construct();
        $t = $this->translation;
        $password = new PasswordInput('password');
        $passwordAgain = new PasswordInput('password_again');
        $this->addInput($t->get('user', 'password'), $password);
        $this->addInput($t->get('user', 'password_again'), $passwordAgain);
        $this->addValidator('password', new PasswordValidator());
        $this->addValidator('password_again', new SameValidator($this, 'password'));
    }

}