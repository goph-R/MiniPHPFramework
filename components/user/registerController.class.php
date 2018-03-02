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
        $this->responseLayout('components/core/templates/layout', 'components/user/templates/register');
    }

    public function activation() {
        $this->message('info',
            $this->translation->get('user', 'activation'),
            $this->translation->get('user', 'activation_sent')
        );
    }

    public function activate() {
        if ($this->userService->activate($this->request->get('hash'))) {
            return $this->redirect('register/success');
        }
        $this->message('error',
            $this->translation->get('user', 'activation'),
            $this->translation->get('user', 'activation_unsuccessful')
        );
    }

    public function success() {
        $this->message('info',
            $this->translation->get('user', 'registration'),
            $this->translation->get('user', 'registration_successful')
        );
    }

    private function message($type, $title, $message) {
        $this->view->set('title', $title);
        $this->view->set('messageType', $type);
        $this->view->set('message', $message);
        $this->responseLayout('components/core/templates/layout', 'components/user/templates/message');
    }

}