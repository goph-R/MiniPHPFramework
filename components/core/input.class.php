<?php

abstract class Input {

    protected $im;

    /**
     * @var View
     */
    protected $view;

    protected $name;
    protected $error;
    protected $defaultValue;
    protected $scripts = [];
    protected $styles = [];
    protected $classes = [];
    protected $value;
    protected $trimValue = true;

    public function __construct(InstanceManager $im, $name, $defaultValue = '') {
        $this->im = $im;
        $this->view = $im->get('view');
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->create();
    }

    public function setTrimValue($trimValue) {
        $this->trimValue = $trimValue;
    }

    public function create() {}

    public function setError($error) {
        $this->error = $error;
    }

    public function getClasses() {
        $classes = $this->classes;
        if ($this->hasError()) {
            $classes[] = 'error';
        }
        return join($classes, ' ');
    }

    public function hasError() {
        return (boolean)$this->error;
    }

    public function getError() {
        return $this->error;
    }

    public function getScripts() {
        return $this->scripts;
    }

    public function getStyles() {
        return $this->styles;
    }

    public function getName() {
        return $this->name;
    }

    public function getValue() {
        return $this->trimValue ? trim($this->value) : $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getDefaultValue() {
        return $this->defaultValue;
    }

}