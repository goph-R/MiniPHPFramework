<?php

class LoginForm extends Form {
	
	public function __construct($request, $view) {
		parent::__construct($request, $view);
	}

	public function create() {
		$notEmptyValidator = new NotEmptyValidator();
		$this->addInput('Email', new TextInput($this->view, 'email'));
		$this->addValidator('email', $notEmptyValidator);
		$this->addInput('Password', new PasswordInput($this->view, 'password'));
		$this->addValidator('password', $notEmptyValidator);
	}
	
}