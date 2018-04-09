<?php

class SelectInput extends Input {

    private $options = [];

    public function __construct($name, $defaultValue='', $options=[]) {
        parent::__construct($name, $defaultValue);
        $this->options = $options;
    }

    public function fetch() {
        $result = '<select';
        $result .= ' id="'.$this->getId().'"';
        $result .= ' name="'.$this->getName().'"';
        $result .= $this->getClassHtml();
        $result .= '>';
        foreach ($this->options as $optionValue => $optionText) {
            $selected = $optionValue == $this->getValue() ? ' selected="selected"' : '';
            $value = $this->view->escape($optionValue);
            $result .= '<option value="'.$value.'"'.$selected.'>'.$optionText.'</option>';
        }
        $result .= '</select>';
        return $result;
    }

}