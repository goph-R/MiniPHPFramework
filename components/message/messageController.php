<?php

abstract class MessageController extends Controller {
    
    /**
     * @var MessageService
     */
    protected $messageService;
    
    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->messageService = $im->get('messageService');
    }
    
}
