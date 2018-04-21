<?php

class MessageFormFactory {

    /**
     * @var MessageService
     */
    private $messageService;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->messageService = $im->get('messageService');
    }

    /**
     * @param mixed
     * @return Form
     */
    public function createWriteForm($message) {
        $form = new Form();
        $defaultSubject = '';
        $defaultText = '';
        if ($message) {
            $defaultSubject = $this->messageService->createReplyTitle($message->get('subject'));
            $defaultText = $this->messageService->createReplyText($message->get('text'));
        }
        $form->addInput(['message', 'subject'], new TextInput('subject', $defaultSubject));
        $form->addInput(['message', 'message'], new TextareaInput('text', $defaultText));
        return $form;
    }
    
    
}

