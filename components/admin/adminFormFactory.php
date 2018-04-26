<?php

class AdminFormFactory {

    /**
     * @var Request
     */
    private $request;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->request = $im->get('request');
    }

    /**
     * @return Form
     */
    public function createFilterForm() {
        $form = new Form();
        $form->addInput('', new TextInput('search', $this->request->get('search')));
        $form->setRequired('search', false);
        return $form;
    }

    /**
     * @param Record
     * @return Form
     */
    public function createForm(Record $record) {
        return null;
    }

}