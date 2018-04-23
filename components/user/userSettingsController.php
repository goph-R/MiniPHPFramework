<?php

class UserSettingsController extends UserController {

    public function index() {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $record = $this->userService->findById($this->user->get('id'));
        $form = $this->formFactory->createSettingsForm($record);
        if ($form->processInput()) {
            $messages = $this->save($form);
            if ($messages) {
                $this->user->setFlash('settings_messages', $messages);
                return $this->redirect('settings');
            }
        }
        $form->setValue('old_password', '');
        $form->setValue('password', '');
        $form->setValue('password_again', '');
        $this->view->set('form', $form);
        return $this->respondLayout(':core/layout', ':user/settings');
    }
    
    protected function save(Form $form) {
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
        if (!$this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $data = ['title' => $this->translation->get('user', 'new_email_address')];
        if ($this->userService->activateNewEmail($this->request->get('id'), $this->request->get('hash'))) {
            $data['messageType'] = 'info';
            $data['message'] = $this->translation->get('user', 'email_activation_successful');
        } else {
            $data['messageType'] = 'error';
            $data['message'] = $this->translation->get('user', 'email_activation_not_found');
        }
        $this->view->set($data);
        return $this->respondLayout(':core/layout', ':user/message');
    }
    
}
