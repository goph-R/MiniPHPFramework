<?php

class LoginForm extends Form {

    public function create() {
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator();
        $this->addInput('Email', new TextInput('email'));
        $this->addValidator('email', $notEmptyValidator);
        $this->addInput($t->get('user', 'password'), new PasswordInput('password'));
        $this->addValidator('password', $notEmptyValidator);
        $this->addInput('', new CheckboxInput('remember', '1', $t->get('user', 'remember_me')));
    }

}