<?php


class ContactFormFactory {

    /**
     * @return Form
     */
    public function createContactForm() {
        $form = new Form();
        $form->addInput('Email', new TextInput('email'));
        $form->addValidator('email', new EmailValidator());
        $form->addInput(['contact', 'name'], new TextInput('name'));
        $form->addInput(['contact', 'message'], new TextareaInput('message'));
        $form->addInput('', new CheckboxInput('check', 1, "Check"));
        return $form;
    }

}