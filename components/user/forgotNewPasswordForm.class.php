<?php

class ForgotNewPasswordForm extends Form {

    public function create() {
        $notEmptyValidator = new NotEmptyValidator();
        $password = new PasswordInput($this->im, 'password');
        $password->setTrimValue(false);
        $passwordAgain = new PasswordInput($this->im, 'password_again');
        $passwordAgain->setTrimValue(false);
        $this->addInput('Password', $password);
        $this->addInput('Password again', $passwordAgain);
        $this->addValidator('password', $notEmptyValidator);
        $this->addValidator('password_again', $notEmptyValidator);
        $this->addValidator('password_again', new SameValidator($password, 'Password'));
    }

}