<?php

class ForgotController extends Controller {

    private $userService;

    public function __construct($im) {
        parent::__construct($im);
        $this->userService = $im->get('userService');
    }

    public function index() {
        if ($this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $form = new ForgotForm($this->im);
        if ($form->processInput()) {
            if ($this->userService->sendForgotEmail($form->getValue('email'))) {
                return $this->redirect('forgot/sent');
            } else {
                $form->addError($this->translator->get('user', 'couldnt_send_email'));
            }
        }
        $this->view->set('form', $form);
        $this->responseLayout(':core/layout', ':user/forgot');
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
        $form = new ForgotNewPasswordForm($this->im);
        if ($form->processInput()) {
            $this->userService->changeForgotPassword($record, $this->getValue('passsword'));
            return $this->redirect('forgot/success');
        }
        $this->view->set('hash', $hash);
        $this->view->set('form', $form);
        $this->responseLayout(':core/layout', ':user/forgotNewPassword');
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