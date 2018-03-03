<?php

class RegisterForm extends Form {

    public function create() {
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator($this->im);
        // email
        $this->addInput('Email', new TextInput($this->im, 'email'));
        $this->addValidator('email', $notEmptyValidator);
        $this->addValidator('email', new EmailValidator($this->im));
        $this->addValidator('email', new EmailExistsValidator($this->im));
        // passwords
        $password = new PasswordInput($this->im, 'password');
        $password->setTrimValue(false);
        $passwordAgain = new PasswordInput($this->im, 'password_again');
        $passwordAgain->setTrimValue(false);
        $passwordLabel = $t->get('user', 'password');
        $this->addInput($passwordLabel, $password);
        $this->addInput($t->get('user', 'password_again'), $passwordAgain);
        $this->addValidator('password', $notEmptyValidator);
        $this->addValidator('password_again', $notEmptyValidator);
        $this->addValidator('password_again', new SameValidator($this->im, $password, $passwordLabel));
        // other data
        $this->addInput($t->get('user', 'firstname'), new TextInput($this->im, 'firstname'));
        $this->addValidator('firstname', $notEmptyValidator);
        $this->addInput($t->get('user', 'lastname'), new TextInput($this->im, 'lastname'));
        $this->addValidator('lastname', $notEmptyValidator);
        $this->addInput($t->get('user', 'country'), new SelectInput($this->im, 'country', 'hu', [
            'hu' => 'Magyarország',
            'de' => 'Deutschland',
            'at' => 'Österreich'
        ]));
        $this->addInput($t->get('user', 'zip'), new TextInput($this->im, 'zip'));
        $this->addValidator('zip', $notEmptyValidator);
        $this->addInput($t->get('user', 'city'), new TextInput($this->im, 'city'));
        $this->addValidator('city', $notEmptyValidator);
    }

}