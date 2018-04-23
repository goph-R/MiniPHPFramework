<?php

class TextareaInput extends Input {

    protected $type = 'text';    
    protected $placeholder = '';

    public function __construct($name, $defaultValue = '') {
        parent::__construct($name, $defaultValue);
        $this->trimValue = false;
    }

    public function fetch() {
        $result = '<textarea';
        $result .= ' id="'.$this->getId().'"';
        $result .= ' name="'.$this->getName().'"';
        if ($this->placeholder) {
            $result .= ' placeholder="'.$this->view->escape($this->placeholder).'"';
        }
        $result .= $this->getClassHtml();
        $result .= '>'.$this->getValue().'</textarea>';
        return $result;
    }

}