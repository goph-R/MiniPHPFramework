<?php

abstract class UserController extends Controller {

    /**
     * @var UserService
     */
    protected $userService;

    public function __construct($im) {
        parent::__construct($im);
        $this->userService = $im->get('userService');
    }

}