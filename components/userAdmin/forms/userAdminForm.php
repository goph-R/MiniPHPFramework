<?php

class UserAdminForm extends AdminForm {

    public function __construct(Record $record) {
        parent::__construct($record);        
        $t = $this->translation;
        $this->addInput('Email', new TextInput('email', $record->get('email')));
        $this->addValidator('email', new NotEmptyValidator());
        $this->addValidator('email', new EmailValidator());
        $this->addValidator('email', new UserAdminEmailExistsValidator($record));
        $this->addInput($t->get('user', 'password'), new TextInput('password', ''));
        $this->addInput('', new CheckboxInput('active', '1', $t->get('userAdmin', 'active'), $record->get('active')));
    }
    
    public function save() {
        $this->record->set('email', $this->getValue('email'));
        if ($this->getValue('password')) {
            $im = InstanceManager::getInstance();
            $userService = $im->get('userService');
            $this->record->set('password', $userService->hash($this->getValue('password')));            
        }
        $this->record->set('active', $this->getValue('active'));
        $this->record->save();
    }

}