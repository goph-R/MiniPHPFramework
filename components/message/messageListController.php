<?php

class MessageListController extends MessageController {
        
    public function index() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect();
        }
        $sent = $this->request->get('sent', false);
        $messages = $this->messageService->findAllByUserIdAndSent($this->user->get('id'), $sent);
        $this->view->set([
            'messages'       => $messages,
            'messageService' => $this->messageService
        ]);
        
        $this->respondLayout(':core/layout', ':message/list');
    }
    
}