<?php

class LoginForm extends Form {

    public function create() {
        $im = $this->im;
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator($im);
        $this->addInput('Email', new TextInput($im, 'email'));
        $this->addValidator('email', $notEmptyValidator);
        $this->addInput($t->get('user', 'password'), new PasswordInput($im, 'password'));
        $this->addValidator('password', $notEmptyValidator);
        $this->addInput('', new CheckboxInput($im, 'remember', '1', $t->get('user', 'remember_me')));
    }

}