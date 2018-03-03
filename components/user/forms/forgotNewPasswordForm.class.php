<?php

class ForgotNewPasswordForm extends Form {

    public function create() {
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator($this->im);
        $password = new PasswordInput($this->im, 'password');
        $password->setTrimValue(false);
        $passwordAgain = new PasswordInput($this->im, 'password_again');
        $passwordAgain->setTrimValue(false);
        $this->addInput($t->get('user', 'password'), $password);
        $this->addInput($t->get('user', 'password_again'), $passwordAgain);
        $this->addValidator('password', $notEmptyValidator);
        $this->addValidator('password_again', $notEmptyValidator);
        $this->addValidator('password_again', new SameValidator($this->im, $password, ''));
    }

}