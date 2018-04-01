<?php

class ForgotForm extends Form {

    public function create() {
        $this->addInput('Email', new TextInput('email'));
        $this->addValidator('email', new NotEmptyValidator());
        $this->addValidator('email', new EmailValidator());
        $this->addValidator('email', new EmailExistsValidator(true));
    }

}