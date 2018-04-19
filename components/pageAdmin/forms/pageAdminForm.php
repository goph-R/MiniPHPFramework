<?php

class PageAdminForm extends AdminForm {
    
    public function __construct(Record $record) {
        parent::__construct($record);
        $this->addInput(['pageAdmin', 'title'], new TextInput('title', $record->get('title')));
        $this->addInput(['pageAdmin', 'content'], new CkEditorInput('content', $record->get('content')));
    }
    
    public function save() {
        $this->record->set('title', $this->getValue('title'));
        $this->record->set('content', $this->getValue('content'));
    }
    
}
