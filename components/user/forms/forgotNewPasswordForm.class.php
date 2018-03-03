<?php

class ForgotNewPasswordForm extends Form {

    public function create() {
        $im = $this->im;
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator($im);
        $password = new PasswordInput($im, 'password');
        $password->setTrimValue(false);
        $passwordAgain = new PasswordInput($im, 'password_again');
        $passwordAgain->setTrimValue(false);
        $this->addInput($t->get('user', 'password'), $password);
        $this->addInput($t->get('user', 'password_again'), $passwordAgain);
        $this->addValidator('password', $notEmptyValidator);
        $this->addValidator('password_again', $notEmptyValidator);
        $this->addValidator('password_again', new SameValidator($im, $password, ''));
    }

}