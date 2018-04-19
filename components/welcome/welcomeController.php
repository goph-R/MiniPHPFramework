<?php

class WelcomeController extends Controller {

    public function index() {
        $this->respondLayout(':core/layout', 'components/welcome/templates/welcome');
    }

}