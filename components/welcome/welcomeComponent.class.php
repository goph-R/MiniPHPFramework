<?php

class WelcomeComponent implements Initiable {

    private $translation;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->translation = $im->get('translation');
    }

    public function init() {
        $this->translation->add('welcome', 'components/welcome/translations');
    }
}