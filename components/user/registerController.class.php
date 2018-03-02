<?php

class RegisterController extends Controller {

    private $userService;

    public function __construct($im) {
        parent::__construct($im);
        $this->userService = $im->get('userService');
    }

    public function index() {
        if ($this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $form = new RegisterForm($this->im);
        if ($form->processInput()) {
            if ($this->userService->register($form->getValues())) {
                return $this->redirect('register/activation');
            } else {
                $form->addError($this->translation->get('user', 'couldnt_send_email'));
            }
        }
        $this->view->set('form', $form);
        $this->responseLayout(':core/layout', ':user/register');
    }

    public function activation() {
        $this->message('info', 'activation', 'activation_sent');
    }

    public function activate() {
        if ($this->userService->activate($this->request->get('hash'))) {
            return $this->redirect('register/success');
        }
        $this->message('error', 'activation', 'activation_unsuccessful');
    }

    public function success() {
        $this->message('info', 'registration', 'registration_successful');
    }

    private function message($type, $title, $message) {
        $this->view->set('messageType', $type);
        $this->view->set('title', $this->translation->get('user', $title));
        $this->view->set('message', $this->translation->get('user', $message));
        $this->responseLayout(':core/layout', ':user/message');
    }

}