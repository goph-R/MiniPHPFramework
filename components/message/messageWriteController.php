<?php

class MessageWriteController extends MessageController {
    
    /**
     * @var MessageFormFactory
     */
    private $formFactory;
    
    /**
     * @var UserService
     */
    private $userService;
    
    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->formFactory = $im->get('messageFormFactory');
        $this->userService = $im->get('userService');
    }    
        
    public function index() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect();
        }
        $recipientId = $this->request->get('recipient_id');
        $this->processForm($recipientId, 'message/write/'.$recipientId, null);
    }

    public function reply() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect();
        }
        $replyTo = $this->request->get('reply_to');
        $message = $this->messageService->findById($replyTo);
        if (!$this->messageService->isOwned($message)) {
            return $this->redirect();
        }
        $replyOwn = $this->user->get('id') == $message->get('sender_id');
        $recipientId = $replyOwn ? $message->get('recipient_id') : $message->get('sender_id');
        $this->focusMessageInput();
        $this->processForm($recipientId, 'message/reply/'.$replyTo, $message);
    }

    protected function focusMessageInput() {
        $this->view->addScriptContent("document.getElementById('form_text').focus();");
    }

    protected function processForm($recipientId, $action, $message) {
        $recipient = $this->userService->findActiveById($recipientId);
        if (!$recipient) {
            return $this->respond404();
        }
        $form = $this->formFactory->createWriteForm($message);
        if ($form->processInput()) {
            $senderId = $this->user->get('id');
            $replyTo = $message ? $message->get('id') : null;
            $this->messageService->send($senderId, $recipientId, $replyTo, $form->getValues());
            $this->user->setFlash('message_sent', $this->translation->get('message', 'message_sent'));
            return $this->redirect('messages');
        }
        $this->view->set([
            'action'      => $action,
            'recipient'   => $recipient,
            'form'        => $form,
            'userService' => $this->userService
        ]);
        $this->respondLayout(':core/layout', ':message/write');
    }
    
}