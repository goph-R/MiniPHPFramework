<?php

class EmailExistsValidator extends Validator {

    private $userService;
    private $needToExists;

    public function __construct($im, $needToExists=false) {
        if ($needToExists) {
            $this->error = "{label} not exists in our database";
        } else {
            $this->error = "{label} exists in our database";
        }
        $this->userService = $im->get('userService');
        $this->needToExists = $needToExists;
    }

    public function doValidate($value) {
        if ($this->userService->findByEmail($value)) {
            return $this->needToExists;
        }
        return !$this->needToExists;
    }

}