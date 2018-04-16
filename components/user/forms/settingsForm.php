<?php

class SettingsForm extends Form {
    
    public function __construct($record) {
        parent::__construct();
        $t = $this->translation;
        $this->addInput('Email', new TextInput('email', $record->get('email')), $t->get('user', 'email_change_description'));
        $this->addValidator('email', new NotEmptyValidator());
        $this->addValidator('email', new EmailValidator());
        $this->addValidator('email', new EmailExistsExceptValidator($record));
        $this->addInput($t->get('user', 'old_password'), new PasswordInput('old_password'), $t->get('user', 'set_if_change_password'));
        $this->setRequired('old_password', false);
        $this->addValidator('old_password', new CurrentPasswordValidator());
        $this->addInput($t->get('user', 'new_password'), new PasswordInput('password'));
        $this->setRequired('new_password', false);
        $this->addValidator('password', new PasswordValidator());
        $this->addInput($t->get('user', 'new_password_again'), new PasswordInput('password_again'));
        $this->addValidator('password_again', new SameValidator($this, 'password'));
    }
    
}