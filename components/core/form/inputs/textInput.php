<?php

class TextInput extends Input {

    protected $type = 'text';

    public function fetch() {
        $result = '<input type="'.$this->type.'"';
        $result .= ' id="'.$this->getId().'"';
        $result .= ' name="'.$this->getName().'"';
        $result .= ' value="'.$this->view->escape($this->getValue()).'"';
        $result .= $this->getClassHtml();
        $result .= '>';
        return $result;
    }

}