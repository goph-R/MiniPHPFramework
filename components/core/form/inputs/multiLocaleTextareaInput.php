<?php

class MultiLocaleTextareaInput extends Input {
    
    private static $scriptAdded = false;
    
    /**
     * @var Translation
     */
    private $translation;
    
    /**
     * @var TextareaInput[]
     */
    private $inputs = [];
    
    public function __construct($name, $defaultValue=[]) {
        parent::__construct($name, $defaultValue);
        $im = InstanceManager::getInstance();
        $this->translation = $im->get('translation');
        foreach ($this->translation->getAllLocales() as $locale) {
            $this->inputs[$locale] = new TextareaInput($name.'['.$locale.']', $defaultValue[$locale]);
        }
        if (!self::$scriptAdded) {
            $allLocales = json_encode($this->translation->getAllLocales());
            $this->view->addScript('components/core/static/multiLocaleTextareaInput.js');
            $this->view->addScriptContent('MultiLocaleTextareaInput.init('.$allLocales.');');
            self::$scriptAdded = true;
        }
    }
    
    public function setForm($form) {
        parent::setForm($form);
        foreach ($this->translation->getAllLocales() as $locale) {
            $this->inputs[$locale]->setForm($form);
        }
    }
    
    public function setValue($value) {
        foreach ($this->inputs as $locale => $input) {
            $input->setValue($value[$locale]);
        }        
    }
    
    public function fetch() {
        $tclass = 'multi-locale-textarea-input-tab';
        $result = '<ul class="'.$tclass.'s">';
        foreach ($this->inputs as $locale => $input) {
            $attrs = ' class="'.$tclass.'" data-locale="'.$locale.'" data-name="'.$this->getName().'"';
            $result .= '<li'.$attrs.'>'.$locale.'</li>';
        }
        $result .= '</ul>';
        $cclass = 'multi-locale-textarea-input-container';
        foreach ($this->inputs as $locale => $input) {            
            $attrs = ' class="'.$cclass.'" data-locale="'.$locale.'" data-name="'.$this->getName().'"';
            $result .= '<div'.$attrs.' style="display: none">';
            $result .= $input->fetch();
            $result .= '</div>';
        }
        return $result;
    }
    
}
