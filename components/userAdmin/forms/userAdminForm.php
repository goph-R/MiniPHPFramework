<?php

class UserAdminForm extends Form {

    public function __construct(Record $record) {
        parent::__construct();
        $this->addInput('Email', new TextInput('email', $record->get('email')));
        $this->addValidator('email', new EmailValidator());
        $this->addValidator('email', new EmailExistsExceptValidator($record));
        if ($record->isNew()) {
            $this->addInput(['user', 'password'], new TextInput('password', ''));
            $this->addValidator('password', new PasswordValidator());
        }
        $activeCheckbox = new CheckboxInput('active', '1', ['userAdmin', 'active'], $record->get('active'));
        $this->addInput('', $activeCheckbox);
    }

}