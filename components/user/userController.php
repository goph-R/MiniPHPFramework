<?php

abstract class UserController extends Controller {

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var UserFormFactory
     */
    protected $formFactory;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->userService = $im->get('userService');
        $this->formFactory = $im->get('userFormFactory');
    }

}