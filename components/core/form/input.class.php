<?php

abstract class Input {

    protected $im;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form
     */
    protected $form;

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
        $this->request = $im->get('request');
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->value = $defaultValue;
        $this->create();
    }

    public function setForm($form) {
        $this->form = $form;
    }

    public function getId() {
        $name = $this->getName();
        $name = preg_replace('/[^0-9a-zA-Z_]+/', '_', $name);
        return $this->form->getName().'_'.$name;
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
        return $classes;
    }

    public function getClassHtml() {
        $classes = $this->getClasses();
        return $classes ? ' class="'.join($classes, ' ').'"' : '';
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