<?php

class EmailExistsExceptValidator extends Validator {

    /**
     * @var UserService
     */
    private $userService;
    
    /**
     * @var Record
     */
    private $exceptRecord;

    public function __construct($exceptRecord=null) {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->userService = $im->get('userService');
        $this->message = $this->translation->get('user', 'email_exists');
        $this->exceptRecord = $exceptRecord;
    }

    public function doValidate($value) {
        return $this->userService->emailExistsExcept($value, $this->exceptRecord);
    }

}