<?php

abstract class Form {

    protected $im;
    protected $view;
    protected $request;
    protected $inputs = [];
    protected $labels = [];
    protected $validators = [];
    protected $postValidators = [];
    protected $errors = [];

    public function __construct($im) {
        $this->im = $im;
        $this->request = $im->get('request');
        $this->view = $im->get('view');
        $this->create();
    }

    abstract function create();

    public function addInput($label, $input) {
        $this->labels[$input->getName()] = $label;
        $this->inputs[$input->getName()] = $input;
    }

    public function getValues() {
        $result = [];
        foreach ($this->inputs as $input) {
            $result[$input->getName()] = $input->getValue();
        }
        return $result;
    }

    public function getInputs() {
        return $this->inputs;
    }

    public function getLabel($inputName) {
        return isset($this->labels[$inputName]) ? $this->labels[$inputName] : '';
    }

    public function hasErrors() {
        return count($this->errors) > 0;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function addError($error) {
        $this->errors[] = $error;
    }

    public function addValidator($inputName, $validator) {
        if (!isset($this->validators[$inputName])) {
            $this->validators[$inputName] = [];
        }
        $this->validators[$inputName][] = $validator;
    }

    public function addPostValidator($validator) {
        $this->postValidators[] = $validator;
    }

    public function getValue($inputName) {
        return $this->inputs[$inputName]->getValue();
    }

    public function bind() {
        $this->errors = [];
        foreach ($this->inputs as $input) {
            $input->setValue($this->request->get($input->getName(), $input->getDefaultValue()));
        }
    }

    public function processInput() {
        if (!$this->request->isPost()) {
            return false;
        }
        $this->bind();
        return $this->validate();
    }

    public function validate() {
        $result = $this->validateInputs();
        if ($result) {
            $result = $this->postValidate();
        }
        return $result;
    }

    private function validateInputs() {
        $result = true;
        foreach ($this->validators as $inputName => $validatorList) {
            foreach ($validatorList as $validator) {
                $subResult = $validator->validate($this->labels[$inputName], $this->inputs[$inputName]->getValue());
                if (!$subResult) {
                    $result = false;
                    $this->inputs[$inputName]->setError($validator->getError());
                    break;
                }
            }
        }
        return $result;
    }

    private function postValidate() {
        $result = true;
        foreach ($this->postValidators as $validator) {
            $subResult = $validator->validate('', null);
            if (!$subResult) {
                $this->errors[] = $validator->getError();
                $result = false;
            }
        }
        return $result;
    }

    public function fetch($path = 'components/core/templates/form') {
        foreach ($this->inputs as $input) {
            foreach ($input->getStyles() as $style) {
                $this->view->addStyle($style);
            }
            foreach ($input->getScripts() as $script) {
                $this->view->addScript($script);
            }
        }
        $this->view->set('form', $this);
        return $this->view->fetch($path);
    }
}
