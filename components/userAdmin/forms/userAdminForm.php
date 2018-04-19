<?php

class UserAdminForm extends AdminForm {

    public function __construct(Record $record) {
        parent::__construct($record);        
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
    
    public function save() {
        $this->record->set('email', $this->getValue('email'));
        if ($this->hasInput('password')) {
            $im = InstanceManager::getInstance();
            $userService = $im->get('userService');
            $this->record->set('password', $userService->hash($this->getValue('password')));            
        }
        $this->record->set('active', $this->getValue('active'));
        $this->record->save();
    }

}