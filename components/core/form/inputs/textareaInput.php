<?php

class TextareaInput extends Input {

    protected $type = 'text';    
    protected $placeholder = '';
    
    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;
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