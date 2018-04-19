<?php

class CheckboxGroupInput extends Input {

    private $checks;
    private $labels;

    public function __construct($name, $labelsByValues=[], $checks=[]) {
        parent::__construct($name, array_keys($labelsByValues));
        $this->checks = $checks;
        $this->labels = array_values($labelsByValues);
        $this->trimValue = false;
        $this->required = false;
    }

    public function setValue($value) {
        parent::setValue($value);
        foreach ($this->defaultValue as $name => $defaultValue) {
            $v = isset($value[$name]) ? $value[$name] : '';
            $this->checks[$name] = $v == $defaultValue;
        }
    }

    public function fetch() { 
        $result = '<div class="checkbox-group">';
        foreach ($this->defaultValue as $name => $defaultValue) {
            $id = $this->getId().'_'.$this->escapeName($name);
            $inputName = $this->getName().'['.$this->escapeName($name).']';
            $result .= '<div class="checkbox-group-row">';
            $result .= '<input type="checkbox" id="'.$id.'" name="'.$inputName.'"';
            $result .= ' value="'.$this->view->escape($defaultValue).'"';
            $result .= $this->getClassHtml();
            if (isset($this->checks[$name]) && $this->checks[$name]) {
                $result .= ' checked="checked"';
            }
            $result .= '>';            
            if (isset($this->labels[$name]) && $this->labels[$name]) {
                $result .= '<label for="'.$id.'">'.$this->labels[$name].'</label>';
            }
            $result .= '</div>';
        }
        $result .= '</div>';
        return $result;
    }

}

