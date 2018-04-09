<?php

class ForgotNewPasswordForm extends Form {

    public function __construct() {
        parent::__construct();
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator();
        $password = new PasswordInput('password', '', true);
        $passwordAgain = new PasswordInput('password_again');
        $this->addInput($t->get('user', 'password'), $password);
        $this->addInput($t->get('user', 'password_again'), $passwordAgain);
        $this->addValidator('password', $notEmptyValidator);
        $this->addValidator('password_again', $notEmptyValidator);
        $this->addValidator('password_again', new SameValidator($password));
    }

}