<?php

class LogoutController extends Controller {

	public function index() {
		$model = new UserModel($this->config, $this->db, $this->user);
		$model->logout();
		$this->redirect($this->config->get('router.base'));
	}

}