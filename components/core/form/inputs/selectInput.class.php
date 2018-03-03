<?php

class SelectInput extends Input {

    private $options = [];

    public function __construct($view, $name, $defaultValue = '', $options = []) {
        parent::__construct($view, $name, $defaultValue);
        $this->options = $options;
    }

    public function fetch() {
        $result = '<select';
        $result .= ' name="'.$this->getName().'"';
        $result .= ' class="'.$this->getClasses().'">';
        foreach ($this->options as $optionValue => $optionText) {
            $selected = $optionValue == $this->getValue() ? ' selected="selected"' : '';
            $value = $this->view->escape($optionValue);
            $result .= '<option value="'.$value.'"'.$selected.'>'.$optionText.'</option>';
        }
        $result .= '</select>';
        return $result;
    }

}