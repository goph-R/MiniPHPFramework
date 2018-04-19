<?php

abstract class AdminFormFactory {

    /**
     * @throws Exception
     * @return Form
     */
    public function createFilterForm() {
        $im = InstanceManager::getInstance();
        $request = $im->get('request');
        $form = new Form();
        $form->addInput('', new TextInput('search', $request->get('search')));
        $form->setRequired('search', false);
        return $form;
    }

    /**
     * @param Record
     * @return Form
     */
    abstract public function createForm(Record $record);

}