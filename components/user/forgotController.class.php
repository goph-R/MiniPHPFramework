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
        if ($this->request->isPost()) {
            $form->bind();
            if ($form->validate()) {
                if ($this->userService->sendForgotEmail($form->getValue('email'))) {
                    return $this->redirect('forgot/sent');
                } else {
                    $form->addError("Couldn't send email.");
                }
            }
        }
        $this->view->set('form', $form);
        $this->responseLayout('components/core/templates/layout', 'components/user/templates/forgot');
    }

    public function sent() {
        return $this->message('info', 'Password changing', 'An email was sent with the instructions.');
    }

    public function newPassword() {
        if ($this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $hash = $this->request->get('hash');
        $record = $this->userService->findByForgotHash($hash);
        if (!$record) {
            return $this->message('error', 'Password changing', 'The activation was not found in our database.');
        }
        $form = new ForgotNewPasswordForm($this->im);
        if ($this->request->isPost()) {
            $form->bind();
            if ($form->validate()) {
                $this->userService->changeForgotPassword($record, $this->getValue('passsword'));
                return $this->redirect('forgot/success');
            }
        }
        $this->view->set('hash', $hash);
        $this->view->set('form', $form);
        $this->responseLayout('components/core/templates/layout', 'components/user/templates/forgotNewPassword');
    }

    public function success() {
        $this->message('info', 'Password changing', 'The password was changed successfully.');
    }

    private function message($type, $title, $message) {
        $this->view->set('title', $title);
        $this->view->set('messageType', $type);
        $this->view->set('message', $message);
        $this->responseLayout('components/core/templates/layout', 'components/user/templates/message');
    }

}