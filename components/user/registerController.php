<?php

class RegisterController extends UserController {

    public function index() {
        if ($this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $im = InstanceManager::getInstance();
        $form = $im->get('registerForm');
        if ($form->processInput()) {
            $values = $form->getValues();
            $record = $this->userService->register($values);
            if ($this->userService->sendRegisterEmail($values, $record->get('activation_hash'))) {
                return $this->redirect('register/activation');
            }
            $form->addError($this->translation->get('user', 'couldnt_send_email'));
        }
        $form->setValue('password', '');
        $form->setValue('password_again', '');
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