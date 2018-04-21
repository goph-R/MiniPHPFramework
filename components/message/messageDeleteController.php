<?php

class MessageDeleteController extends MessageController {

    public function index() {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect();
        }
        $id = $this->request->get('id');
        $message = $this->messageService->findById($id);
        if (!$message || !$this->messageService->isOwned($message)) {
            return $this->redirect();
        }
        $message->delete();
        $this->redirect('messages');
    }

}