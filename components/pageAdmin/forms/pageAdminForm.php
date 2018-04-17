<?php

class PageAdminForm extends AdminForm {
    
    public function __construct(Record $record) {
        parent::__construct($record);
        $t = $this->translation;
        $this->addInput($t->get('pageAdmin', 'title'), new TextInput('title', $record->get('title')));
        $this->addInput($t->get('pageAdmin', 'content'), new CkEditorInput('content', $record->get('content')));
    }
    
    public function save() {
        $this->record->set('title', $this->getValue('title'));
        $this->record->set('content', $this->getValue('content'));
    }
    
}
