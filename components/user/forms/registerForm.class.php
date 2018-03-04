<?php

class RegisterForm extends Form {

    public function create() {
        $im = $this->im;
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator($im);
        $this->addInput('Email', new TextInput($im, 'email'));
        $this->addValidator('email', $notEmptyValidator);
        $this->addValidator('email', new EmailValidator($im));
        $this->addValidator('email', new EmailExistsValidator($im));
        $password = new PasswordInput($im, 'password');
        $password->setTrimValue(false);
        $passwordAgain = new PasswordInput($im, 'password_again');
        $passwordAgain->setTrimValue(false);
        $passwordLabel = $t->get('user', 'password');
        $this->addInput($passwordLabel, $password);
        $this->addInput($t->get('user', 'password_again'), $passwordAgain);
        $this->addValidator('password', $notEmptyValidator);
        $this->addValidator('password_again', $notEmptyValidator);
        $this->addValidator('password_again', new SameValidator($im, $password, $passwordLabel));
    }

}