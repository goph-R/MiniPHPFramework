<?php

class WelcomeController extends Controller {
    public function index() {
        $this->responseLayout(':core/layout', 'components/welcome/templates/welcome');
    }
}