<?php

class ForgotForm extends Form {

    public function create() {
        $im = $this->im;
        $this->addInput('Email', new TextInput($im, 'email'));
        $this->addValidator('email', new NotEmptyValidator($im));
        $this->addValidator('email', new EmailValidator($im));
        $this->addValidator('email', new EmailExistsValidator($im, true));
    }

}