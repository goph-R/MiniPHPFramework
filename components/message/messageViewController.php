<?php

class MessageViewController extends MessageController {
        
    public function index() {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $message = $this->messageService->findById($this->request->get('id'));
        if (!$this->messageService->isOwned($message)) {
            return $this->redirect();
        }
        $this->messageService->markAsRead($message);
        $this->view->set([
            'message'        => $message,
            'messageService' => $this->messageService
        ]);
        $this->confirmScript->add();
        $this->respondLayout(':core/layout', ':message/view');
    }

    
}