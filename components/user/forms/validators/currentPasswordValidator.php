<?php


class CurrentPasswordValidator extends Validator {

    /**
     * @var User
     */
    private $user;
    
    /**
     * @var UserService
     */
    private $userService;
    
    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->user = $im->get('user');
        $this->userService = $im->get('userService');
        $this->message = $this->translation->get('user', 'current_password_mismatch');
    }

    public function doValidate($value) {
        if ($value && $this->userService->hash($value) != $this->user->get('password')) {
            return false;
        }
        return true;
    }

}
