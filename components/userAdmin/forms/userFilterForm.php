<?php

class UserFilterForm extends Form {
    
    public function __construct() {
        parent::__construct();
        $this->addInput('', new TextInput('search', $this->request->get('search')));
    }
    
}
