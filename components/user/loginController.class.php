<?php

class LoginController extends Controller {

	public function index() {
		if ($this->user->isLoggedIn()) {
			return $this->redirect($this->config->get('router.base'));
		}		
		$form = new LoginForm($this->request, $this->view);
		if ($this->request->isPost()) {
			$form->bind();
			if ($form->validate()) {
				$model = new UserModel($this->config, $this->db, $this->user);				
				if ($model->login($form->getValue('email'), $form->getValue('password'))) {
					return $this->redirect($this->config->get('router.base'));
				} else {
					$form->addError('No such email/password');
				}
			}
		}
		$this->view->set('form', $form);
		$this->responseLayout('components/core/templates/layout', 'components/user/templates/login');
	}
	
}