<?php

class ProfileController extends Controller {
	
	public function index() {
		$model = new UserModel($this->config, $this->db, $this->user);		
		$this->responseLayout('templates/layout', 'templates/profile');
	}
	
}