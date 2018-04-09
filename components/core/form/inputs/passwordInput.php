<?php

class PasswordInput extends TextInput {
    
    private static $scriptAdded = false;
    private $clientCheck;

    public function __construct($name, $defaultValue='', $clientCheck=false) {
        parent::__construct($name, $defaultValue);
        $this->type = 'password';
        $this->clientCheck = $clientCheck;
        $this->trimValue = false;
    }
    
    public function fetch() { 
        if ($this->clientCheck) {
            $this->addScript();
            $this->view->addScriptContent("PasswordCheck.add('#".$this->getId()."');");
        }
        return parent::fetch();
    }
    
    private function addScript() {
        if (self::$scriptAdded) {
            return;
        }
        $im = InstanceManager::getInstance();
        $t = $im->get('translation');
        $texts = [
            'low'    => $t->get('core', 'password_low'),
            'normal' => $t->get('core', 'password_normal'),
            'high'   => $t->get('core', 'password_high')
        ];
        $this->view->addScript('components/core/static/js/passwordCheck.js');
        $this->view->addScriptContent('PasswordCheck.texts = '.json_encode($texts).';');
        self::$scriptAdded = true;
    }    
}