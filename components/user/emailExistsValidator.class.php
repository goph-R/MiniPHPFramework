<?php

class EmailExistsValidator extends Validator {

    /**
     * @var UserService
     */
    private $userService;
    private $needToExists;

    public function __construct($im, $needToExists=false) {
        parent::__construct($im);
        if ($needToExists) {
            $this->error = $this->translation->get('user', 'email_not_exists');
        } else {
            $this->error = $this->translation->get('user', 'email_exists');
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