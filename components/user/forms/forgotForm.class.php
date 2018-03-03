<?php

class ForgotForm extends Form {

    public function create() {
        $this->addInput('Email', new TextInput($this->im, 'email'));
        $this->addValidator('email', new NotEmptyValidator($this->im));
        $this->addValidator('email', new EmailValidator($this->im));
        $this->addValidator('email', new EmailExistsValidator($this->im, true));
    }

}