<?php

class MessageViewController extends MessageController {
        
    public function index() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect();
        }
        $message = $this->messageService->findById($this->request->get('id'));
        if ($message->get('user_id') != $this->user->get('id')) {
            $this->redirect();
        }        
        $this->view->set([
            'message'        => $message,
            'messageService' => $this->messageService
        ]);
        $this->respondLayout(':core/layout', ':message/view');
    }
    
}