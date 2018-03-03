<?php

class LogoutController extends UserController {

    public function index() {
        $this->userService->logout();
        $this->redirect();
    }

}