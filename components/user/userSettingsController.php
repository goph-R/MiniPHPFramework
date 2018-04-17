<?php

class UserSettingsController extends UserController {

    public function index() {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $userRecord = $this->userService->findById($this->user->get('id'));
        $im = InstanceManager::getInstance();
        $form = $im->get('userSettingsForm', [$userRecord]);
        if ($form->processInput()) {
            $messages = $this->saveChanges($form);
            if ($messages) {
                $this->user->setFlash('settings_messages', $messages);
                return $this->redirect('settings');
            }
        }        
        $form->setValue('old_password', '');
        $form->setValue('password', '');
        $form->setValue('password_again', '');
        $this->view->set('form', $form);
        return $this->responseLayout(':core/layout', ':user/settings');
    }
    
    protected function saveChanges($form) {
        $messages = [];
        if ($form->getValue('old_password') && $form->getValue('password')) {
            $this->userService->changePassword($this->user->get('id'), $form->getValue('password'));
            $messages[] = $this->translation->get('user', 'password_changed');
        }
        $email = $form->getValue('email');
        $id = $this->user->get('id');
        if ($email != $this->user->get('email') &&
            $this->userService->saveNewEmail($id, $email)) {            
            $hash = $this->user->get('new_email_hash');            
            if ($this->userService->sendNewAddressEmail($email, $id, $hash)) {
                $messages[] = $this->translation->get('user', 'new_email_was_set');
            }            
        }
        return $messages;
    }
    
    public function activate() {
        $this->view->set('title', $this->translation->get('user', 'new_email_address'));
        if ($this->userService->activateNewEmail($this->request->get('id'), $this->request->get('hash'))) {
            $this->view->set('messageType', 'info');
            $this->view->set('message', $this->translation->get('user', 'email_activation_successful'));
        } else {
            $this->view->set('messageType', 'error');
            $this->view->set('message', $this->translation->get('user', 'email_activation_not_found'));            
        }
        return $this->responseLayout(':core/layout', ':user/message');
    }
    
}
