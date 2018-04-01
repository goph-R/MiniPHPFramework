<?php

class EmailExistsValidator extends Validator {

    /**
     * @var UserService
     */
    private $userService;
    private $needToExists;

    public function __construct($needToExists=false) {
        parent::__construct();
        if ($needToExists) {
            $this->error = $this->translation->get('user', 'email_not_exists');
        } else {
            $this->error = $this->translation->get('user', 'email_exists');
        }
        $im = InstanceManager::getInstance();
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