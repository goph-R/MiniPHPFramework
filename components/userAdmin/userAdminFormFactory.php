<?php

class UserAdminFormFactory extends AdminFormFactory {

    public function createForm(Record $record) {
        $form = new Form();
        $form->addInput('Email', new TextInput('email', $record->get('email')));
        $form->addValidator('email', new EmailValidator());
        $form->addValidator('email', new EmailExistsExceptValidator($record));
        if ($record->isNew()) {
            $form->addInput(['user', 'password'], new TextInput('password', ''));
            $form->addValidator('password', new PasswordValidator());
        }
        $form->addInput('', new CheckboxInput('active', '1', ['userAdmin', 'active'], $record->get('active')));
        return $form;
    }

}