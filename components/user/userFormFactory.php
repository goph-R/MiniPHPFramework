<?php

class UserFormFactory {

    public function createLoginForm() {
        $form = new Form();
        $form->addInput('Email', new TextInput('email'));
        $form->addInput(['user', 'password'], new PasswordInput('password'));
        $form->addInput('', new CheckboxInput('remember', '1', ['user', 'remember_me']));
        return $form;
    }

    public function createRegisterForm() {
        $form = new Form();
        $form->addInput('Email', new TextInput('email'));
        $form->addValidator('email', new EmailValidator());
        $form->addValidator('email', new EmailExistsValidator());
        $password = new PasswordInput('password');
        $passwordAgain = new PasswordInput('password_again');
        $form->addInput(['user', 'password'], $password);
        $form->addInput(['user', 'password_again'], $passwordAgain);
        $form->addValidator('password', new PasswordValidator());
        $form->addValidator('password_again', new SameValidator($form, 'password'));
        return $form;
    }

    public function createSettingsForm(Record $record) {
        $im = InstanceManager::getInstance();
        $t = $im->get('translation');
        $emailDesc = $t->get('user', 'email_change_description');
        if ($record->get('new_email')) {
            $emailDesc = $t->get('user', 'waits_for_activation', ['email' => $record->get('new_email')]);
        }
        $form = new Form();
        $form->addInput('Email', new TextInput('email', $record->get('email')), $emailDesc);
        $form->addValidator('email', new EmailValidator());
        $form->addValidator('email', new EmailExistsExceptValidator($record));
        $form->addInput(['user', 'old_password'], new PasswordInput('old_password'), $t->get('user', 'set_if_change_password'));
        $form->addValidator('old_password', new CurrentPasswordValidator());
        $form->setRequired('old_password', false);
        $form->addInput(['user', 'new_password'], new PasswordInput('password'));
        $form->addValidator('password', new PasswordValidator());
        $form->setRequired('password', false);
        $form->addInput(['user', 'new_password_again'], new PasswordInput('password_again'));
        $form->addValidator('password_again', new SameValidator($form, 'password'));
        $form->setRequired('password_again', false);
        return $form;
    }
    
    public function createNewPasswordForm() {
        $form = new Form();
        $form->addInput(['user', 'password'], new PasswordInput('password'));
        $form->addInput(['user', 'password_again'], new PasswordInput('password_again'));
        $form->addValidator('password', new PasswordValidator());
        $form->addValidator('password_again', new SameValidator($form, 'password'));
        return $form;
    }
    
    public function createForgotForm() {
        $form = new Form();
        $form->addInput('Email', new TextInput('email'));
        $form->addValidator('email', new EmailValidator());
        $form->addValidator('email', new EmailExistsValidator(true));
        return $form;
    }    

}