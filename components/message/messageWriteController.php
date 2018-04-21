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
        $toUserId = $this->request->get('user_id');
        $userRecord = $this->userService->findById($toUserId);
        if (!$userRecord) {
            $this->respond404();
        }
        $form = $this->formFactory->createWriteForm();
        if ($form->processInput()) {            
            $this->messageService->send($this->user->get('id'), $toUserId, $form->getValue('message'));
            $this->user->setFlash('message_sent', $this->translation->get('message', 'message_sent'));
            return $this->redirect('messages');
        }
        $this->view->set('toUserId', $toUserId);
        $this->view->set('form', $form);
        $this->respondLayout(':core/layout', ':message/write');
    }
    
}