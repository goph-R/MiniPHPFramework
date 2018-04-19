<?php

class AdminSearchForm extends Form {
    
    public function __construct() {
        parent::__construct();
        $this->addInput('', new TextInput('search', $this->request->get('search')));
        $this->setRequired('search', false);
    }
    
}
