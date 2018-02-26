<?php

class LoginController extends Controller {

    private $userService;

    public function __construct($im) {
        parent::__construct($im);
        $this->userService = $im->get('userService');
    }

	public function index() {
		if ($this->user->isLoggedIn()) {
			return $this->redirect();
		}		
		$form = new LoginForm($this->im);
		if ($this->request->isPost()) {
			$form->bind();
			if ($form->validate()) {
				if ($this->userService->login($form->getValue('email'), $form->getValue('password'))) {
					return $this->redirect();
				} else {
					$form->addError('No such email or password');
				}
			}
		}
		$this->view->set('form', $form);
		$this->responseLayout('components/core/templates/layout', 'components/user/templates/login');
	}

}