<?php

class WelcomeComponent {

    private $translation;

    public function __construct($im) {
        $this->translation = $im->get('translation');
    }

    public function init() {
        $this->translation->add('welcome', 'components/welcome/translations');
    }
}