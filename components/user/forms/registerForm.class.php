<?php

class RegisterForm extends Form {

    public function create() {
        $im = $this->im;
        $t = $this->translation;
        $notEmptyValidator = new NotEmptyValidator($im);
        // email
        $this->addInput('Email', new TextInput($im, 'email'));
        $this->addValidator('email', $notEmptyValidator);
        $this->addValidator('email', new EmailValidator($im));
        $this->addValidator('email', new EmailExistsValidator($im));
        // passwords
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
        // other data
        $this->addInput($t->get('user', 'firstname'), new TextInput($im, 'firstname'));
        $this->addValidator('firstname', $notEmptyValidator);
        $this->addInput($t->get('user', 'lastname'), new TextInput($im, 'lastname'));
        $this->addValidator('lastname', $notEmptyValidator);
        $this->addInput($t->get('user', 'country'), new SelectInput($im, 'country', 'hu', [
            'hu' => 'Magyarország',
            'de' => 'Deutschland',
            'at' => 'Österreich'
        ]));
        $this->addInput($t->get('user', 'zip'), new TextInput($im, 'zip'));
        $this->addValidator('zip', $notEmptyValidator);
        $this->addInput($t->get('user', 'city'), new TextInput($im, 'city'));
        $this->addValidator('city', $notEmptyValidator);
    }

}