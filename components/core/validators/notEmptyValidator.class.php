<?php

class NotEmptyValidator extends Validator {
	
	public function __construct() {
		$this->error = "{label} can't be empty";
	}

	public function doValidate($value) {
		if (!$value) {
			return false;
		}
		return true;
	}

}