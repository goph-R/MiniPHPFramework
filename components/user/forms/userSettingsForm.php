<?php

class UserSettingsForm extends Form {
    
    public function __construct($record) {
        parent::__construct();
        $t = $this->translation;
        $emailDesc = $t->get('user', 'email_change_description');
        if ($record->get('new_email')) {
            $emailDesc = $t->get('user', 'waits_for_activation', ['email' => $record->get('new_email')]);
        }        
        $this->addInput('Email', new TextInput('email', $record->get('email')), $emailDesc);
        $this->addValidator('email', new EmailValidator());
        $this->addValidator('email', new EmailExistsExceptValidator($record));
        $passwordDesc = $t->get('user', 'set_if_change_password');
        $this->addInput(['user', 'old_password'], new PasswordInput('old_password'), $passwordDesc);
        $this->addValidator('old_password', new CurrentPasswordValidator());
        $this->setRequired('old_password', false);
        $this->addInput(['user', 'new_password'], new PasswordInput('password'));
        $this->addValidator('password', new PasswordValidator());
        $this->setRequired('password', false);
        $this->addInput(['user', 'new_password_again'], new PasswordInput('password_again'));
        $this->addValidator('password_again', new SameValidator($this, 'password'));
        $this->setRequired('password_again', false);
    }
    
}