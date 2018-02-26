<?php

class RegisterForm extends Form {

    public function create() {
        $notEmptyValidator = new NotEmptyValidator();
        // email
        $this->addInput('Email', new TextInput($this->im, 'email'));
        $this->addValidator('email', $notEmptyValidator);
        $this->addValidator('email', new EmailValidator());
        $this->addValidator('email', new EmailExistsValidator($this->im));
        // passwords
        $password = new PasswordInput($this->im, 'password');
        $password->setTrimValue(false);
        $passwordAgain = new PasswordInput($this->im, 'password_again');
        $passwordAgain->setTrimValue(false);
        $this->addInput('Password', $password);
        $this->addInput('Password again', $passwordAgain);
        $this->addValidator('password', $notEmptyValidator);
        $this->addValidator('password_again', $notEmptyValidator);
        $this->addValidator('password_again', new SameValidator($password, 'Password'));
        // other data
        $this->addInput('Firstname', new TextInput($this->im, 'firstname'));
        $this->addValidator('firstname', $notEmptyValidator);
        $this->addInput('Lastname', new TextInput($this->im, 'lastname'));
        $this->addValidator('lastname', $notEmptyValidator);
        $this->addInput('Country', new SelectInput($this->im, 'country', 'hu', [
            'hu' => 'Magyarország',
            'de' => 'Deutschland',
            'at' => 'Österreich'
        ]));
        $this->addInput('Postal code', new TextInput($this->im, 'zip'));
        $this->addValidator('zip', $notEmptyValidator);
        $this->addInput('City', new TextInput($this->im, 'city'));
        $this->addValidator('city', $notEmptyValidator);
    }

}