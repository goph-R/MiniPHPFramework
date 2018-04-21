<?php

class ConfirmScript {

    /**
     * @var View
     */
    protected $view;

    /**
     * @var Translation
     */
    protected $translation;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->view = $im->get('view');
        $this->translation = $im->get('translation');
    }

    public function add() {
        $this->view->addScript('components/core/static/confirm.js');
        $this->view->addScriptContent('Confirm.texts = '.json_encode($this->getTexts()).';');
    }

    protected function getTexts() {
        return [
            'delete' => $this->translation->get('core', 'confirm_delete')
        ];
    }

}