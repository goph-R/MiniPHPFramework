<?php 

abstract class Validator {

	protected $error = '';
	protected $replacedError = '';

	public function validate($label, $input) {
		$this->replacedError = str_replace('{label}', $label, $this->error);
		return $this->doValidate($input);
	}

	public function getError() {
		return $this->replacedError;
	}

	abstract function doValidate($input);

}