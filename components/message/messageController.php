<?php

abstract class MessageController extends Controller {
    
    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var ConfirmScript
     */
    protected $confirmScript;
    
    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->messageService = $im->get('messageService');
        $this->confirmScript = $im->get('confirmScript');
    }

}
