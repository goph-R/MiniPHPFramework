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
        if ($form->processInput()) {
            if ($this->userService->login($form->getValue('email'), $form->getValue('password'))) {
                return $this->redirect();
            } else {
                $form->addError($this->translator->get('user', 'email_password_not_found'));
            }
        }
        $this->view->set('form', $form);
        $this->responseLayout(':core/layout', ':user/login');
    }

}