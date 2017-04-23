<?php

class WelcomeController extends Controller {
	public function index() {		
		$this->responseLayout('components/core/templates/layout', 'components/welcome/templates/welcome');
	}
}