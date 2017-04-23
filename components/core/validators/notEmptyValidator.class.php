<?php

class NotEmptyValidator extends Validator {
	
	public function __construct() {
		$this->error = "{label} can't be empty";
	}

	public function doValidate($input) {
		if (!$input->getValue()) {
			return false;
		}
		return true;
	}

}