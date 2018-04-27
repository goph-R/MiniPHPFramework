<?php

class ContactController extends Controller {

    /**
     * @var ContactService
     */
    private $contactService;

    /**
     * @var ContactFormFactory
     */
    private $contactFormFactory;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->contactService = $im->get('contactService');
        $this->contactFormFactory = $im->get('contactFormFactory');
    }

    public function index() {
        $form = $this->contactFormFactory->createContactForm();
        if ($this->contactService->processForm($form)) {
            return $this->redirect('contact');
        }
        $this->view->set('form', $form);
        $this->respondLayout(':core/layout', ':contact/contact');
    }

}