<?php

abstract class Form {
	
	protected $view;
	protected $request;
	protected $inputs = [];	
	protected $labels = [];
	protected $validators = [];
	protected $postValidators = [];
	protected $errors = [];

	public function __construct($request, $view) {
		$this->request = $request;
		$this->view = $view;
		$this->create();
	}

	abstract function create();

	public function addInput($label, $input) {
		$this->labels[$input->getName()] = $label;
		$this->inputs[$input->getName()] = $input;
	}

	public function getInputs() {
		return $this->inputs;
	}

	public function getLabel($inputName) {
		return array_key_exists($inputName, $this->labels) ? $this->labels[$inputName] : '';
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
		if (!array_key_exists($inputName, $this->validators)) {
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
				$subResult = $validator->validate($this->labels[$inputName], $this->inputs[$inputName]);				
				$result &= $subResult;
				if (!$subResult) {
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
			$result &= $subResult;
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
