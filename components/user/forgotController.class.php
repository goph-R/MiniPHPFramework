<?php

class ForgotController extends UserController {

    public function index() {
        if ($this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $form = new ForgotForm();
        if ($form->processInput()) {
            if ($this->userService->sendForgotEmail($form->getValue('email'))) {
                return $this->redirect('forgot/sent');
            } else {
                $form->addError($this->translation->get('user', 'couldnt_send_email'));
            }
        }
        $this->view->set('form', $form);
        return $this->responseLayout(':core/layout', ':user/forgot');
    }

    public function sent() {
        return $this->message('info', 'password_changing', 'email_sent_with_instructions');
    }

    public function newPassword() {
        if ($this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $hash = $this->request->get('hash');
        $record = $this->userService->findByForgotHash($hash);
        if (!$record) {
            return $this->message('error', 'password_changing', 'activation_not_found');
        }
        $form = new ForgotNewPasswordForm();
        if ($form->processInput()) {
            $this->userService->changeForgotPassword($record, $form->getValue('password'));
            return $this->redirect('forgot/success');
        }
        $form->setValue('password', '');
        $form->setValue('password_again', '');
        $this->view->set('hash', $hash);
        $this->view->set('form', $form);
        return $this->responseLayout(':core/layout', ':user/forgotNewPassword');
    }

    public function success() {
        $this->message('info', 'password_changing', 'password_changed');
    }

    private function message($type, $title, $message) {
        $this->view->set('messageType', $type);
        $this->view->set('title', $this->translation->get('user', $title));
        $this->view->set('message', $this->translation->get('user', $message));
        $this->responseLayout(':core/layout', ':user/message');
    }

}