<?php

class PageAdminForm extends Form {
    
    public function __construct(Record $record) {
        parent::__construct();
        $this->addInput(['pageAdmin', 'title'], new TextInput('title', $record->get('title')));
        $this->addInput(['pageAdmin', 'content'], new CkEditorInput('content', $record->get('content')));
    }

}
