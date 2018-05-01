<?php

class ContactService {

    /**
     * @var Table
     */
    private $table;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Translation
     */
    private $translation;
    
    /**
     * @var Config
     */
    private $config;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->user = $im->get('user');
        $this->mailer = $im->get('mailer');
        $this->translation = $im->get('translation');
        $this->config = $im->get('config');
        $tableFactory = $im->get('contactTableFactory');
        $this->table = $tableFactory->createContact();

    }

    private function add($values) {
        $record = $this->table->createRecord();
        $fields = ['email', 'name', 'message', 'created_on'];
        $values['created_on'] = time();
        $record->setAll($fields, $values);
        $record->save();
    }

    private function sendEmail($values) {
        $this->mailer->init();
        $this->mailer->addAddress($this->config->get('application.admin_email'));
        $this->mailer->set($values);
        return $this->mailer->send('Contact', ':contact/email');
    }

    public function processForm(Form $form) {
        if (!$form->processInput()) {
            return false;
        }
        if ($form->getValue('check')) {
            return false;
        }
        $values = $form->getValues();
        $this->add($values);
        if (!$this->sendEmail($values)) {
            $form->addError($this->translation->get('contact', 'failure'));
            return false;
        }
        $this->user->setFlash('contact_message', $this->translation->get('contact', 'success'));
        return true;
    }


}