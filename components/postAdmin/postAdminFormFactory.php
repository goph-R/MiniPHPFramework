<?php

class PostAdminFormFactory extends AdminFormFactory {

    public function createForm(Record $record) {
        $form = new Form();
        $form->addInput(['postAdmin', 'title'], new TextInput('title', $record->get('title')));
        $form->addInput(['postAdmin', 'lead'], new TextareaInput('lead', $record->get('lead')));
        $form->addInput(['postAdmin', 'content'], new CkEditorInput('content', $record->get('content')));
        $form->addInput('', new CheckboxInput('active', '1', ['core', 'active'], $record->get('active')));
        return $form;
    }

}