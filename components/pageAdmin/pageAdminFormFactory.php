<?php

class PageAdminFormFactory extends AdminFormFactory {

    public function createForm(Record $record) {
        $form = new Form();
        $form->addInput(['pageAdmin', 'title'], new TextInput('title', $record->get('title')));
        $form->addInput(['pageAdmin', 'content'], new CkEditorInput('content', $record->get('content')));
        return $form;
    }

}