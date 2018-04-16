<?php

class SettingsController extends UserController {

    public function index() {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $userRecord = $this->userService->findById($this->user->get('id'));        
        $im = InstanceManager::getInstance();
        $form = $im->get('settingsForm', [$userRecord]);
        if ($form->processInput()) {
            
        }
        $form->setValue('old_password', '');
        $form->setValue('password', '');
        $form->setValue('password_again', '');
        $this->view->set('form', $form);
        return $this->responseLayout(':core/layout', ':user/settings');
    }
    
}
