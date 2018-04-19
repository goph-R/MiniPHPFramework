<?php

class LoginController extends UserController {

    public function index() {
        if ($this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $form = $this->formFactory->createLoginForm();
        if ($form->processInput()) {
            $email = $form->getValue('email');
            $password = $form->getValue('password');
            $remember = $form->getValue('remember');
            if ($this->userService->login($email, $password, $remember)) {
                return $this->redirect();
            } else {
                $form->addError($this->translation->get('user', 'email_password_not_found'));
            }
        }
        $form->setValue('password', '');
        $this->view->set('form', $form);
        $this->respondLayout(':core/layout', ':user/login');
    }

}