<?php

abstract class UserController extends Controller {

    /**
     * @var UserService
     */
    protected $userService;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->userService = $im->get('userService');
    }

}