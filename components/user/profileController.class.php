<?php

class ProfileController extends Controller {
	
	public function index() {
		$model = new UserModel($this->config, $this->db, $this->user);
		$this->responseLayout('components/core/templates/layout', 'components/user/templates/profile');
	}
	
}