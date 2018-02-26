<?php

class LogoutController extends Controller {

    public function index() {
        $service = $this->im->get('userService');
        $service->logout();
        $this->redirect();
    }

}