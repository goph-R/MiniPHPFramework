<?php

abstract class Form {

    /**
     * @var InstanceManager
     */
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
     * @var Translation
     */
    protected $translation;

    /**
     * @var Input[]
     */
    protected $inputs = [];
    protected $order = [];
    protected $labels = [];

    /**
     * @var Validator[][]
     */
    protected $validators = [];

    /**
     * @var Validator[]
     */
    protected $postValidators = [];
    protected $errors = [];
    protected $name = 'form';

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->request = $im->get('request');
        $this->view = $im->get('view');
        $this->translation = $im->get('translation');
    }

    public function getName() {
        return $this->name;
    }

    public function addInput($label, Input $input, $description='') {
        $name = $input->getName();
        if (!in_array($name, $this->order)) {
            $this->order[] = $name;
        }
        $this->labels[$name] = $label;
        $this->inputs[$name] = $input;
        $input->setDescription($description);
        $input->setForm($this);
    }

    public function getValues() {
        $result = [];
        foreach ($this->inputs as $input) {
            $result[$input->getName()] = $input->getValue();
        }
        return $result;
    }

    public function getInputs() {
        $result = [];
        foreach ($this->order as $name) {
            $result[] = $this->inputs[$name];
        }
        return $result;
    }
    
    public function hasInput($inputName) {
        return isset($this->inputs[$inputName]);
    }

    public function getLabel($inputName) {
        return isset($this->labels[$inputName]) ? $this->labels[$inputName] : '';
    }

    public function getId($inputName) {
        return isset($this->inputs[$inputName]) ? $this->inputs[$inputName]->getId() : '';
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

    public function setValue($inputName, $value) {
        if (isset($this->inputs[$inputName])) {
            $this->inputs[$inputName]->setValue($value);
        }
    }

    public function bind() {
        $this->errors = [];
        foreach ($this->inputs as $input) {
            $name = $input->getName();
            $value = $this->request->get($name, null);
            $input->setValue($value);
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

    public function fetch($path = ':core/form') {
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
