<?php

class ProfileController extends Controller {
	
	public function index() {
		// TODO: check permission
		$model = new UserModel($this->config, $this->db, $this->user);
		$record = $model->findById($this->request->get('id'));
		// TODO: 404
		$this->view->set('record', $record);
		$this->responseLayout('components/core/templates/layout', 'components/user/templates/profile');
	}
	
}