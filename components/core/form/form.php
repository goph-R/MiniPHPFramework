<?php

class Form {

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
        $this->inputs[$name] = $input;
        if (is_array($label) && count($label) == 2) {
            $input->setLabel($this->translation->get($label[0], $label[1]));
        } else {
            $input->setLabel($label);
        }
        if (is_array($description) && count($description) == 2) {
            $input->setDescription($this->translation->get($description[0], $description[1]));
        } else {
            $input->setDescription($description);
        }
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
    
    public function checkInputExistance($inputName) {
        if (!$this->hasInput($inputName)) {
            throw new Exception("Input doesn't exist: $inputName");
        }
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
        $this->checkInputExistance($inputName);
        if (!isset($this->validators[$inputName])) {
            $this->validators[$inputName] = [];
        }
        $this->validators[$inputName][] = $validator;
    }

    public function addPostValidator($validator) {
        $this->postValidators[] = $validator;
    }

    public function getValue($inputName) {
        $this->checkInputExistance($inputName);
        return $this->inputs[$inputName]->getValue();
    }

    public function setValue($inputName, $value) {
        $this->checkInputExistance($inputName);
        $this->inputs[$inputName]->setValue($value);
    }
    
    public function setRequired($inputName, $required) {
        $this->checkInputExistance($inputName);
        $this->inputs[$inputName]->setRequired($required);
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
        foreach ($this->inputs as $inputName => $input) {
            if ($input->isRequired() && $input->isEmpty()) {
                $error = $this->translation->get('core', 'cant_be_empty');
                $input->setError($error);
                $result = false;
            } else if (!$input->isRequired() && $input->isEmpty()) {
                continue;
            } else if (isset($this->validators[$inputName])) {
                $validatorList = $this->validators[$inputName];
                $result &= $this->validateInput($input, $validatorList);
            }
        }
        return $result;
    }
    
    private function validateInput($input, $validatorList) {
        foreach ($validatorList as $validator) {
            $result = $validator->validate($input->getLabel(), $input->getValue());
            if (!$result) {
                $input->setError($validator->getMessage());
                return false;
            }                
        }        
        return true;
    }

    private function postValidate() {
        $result = true;
        foreach ($this->postValidators as $validator) {
            $subResult = $validator->validate('', null);
            if (!$subResult) {
                $this->errors[] = $validator->getMessage();
                $result = false;
            }
        }
        return $result;
    }

    public function fetch($path = ':core/form') {
        foreach ($this->inputs as $input) {
            foreach ($input->getStyles() as $style => $media) {
                $this->view->addStyle($style, $media);
            }
            foreach ($input->getScripts() as $script) {
                $this->view->addScript($script);
            }
        }
        $this->view->set('form', $this);
        return $this->view->fetch($path);
    }

}
