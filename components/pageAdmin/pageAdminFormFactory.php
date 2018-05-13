<?php

class PageAdminFormFactory extends AdminFormFactory {

    /**
     * @var Router
     */
    private $router;
    
    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->router = $im->get('router');        
    }
    
    public function createForm(Record $record) {
        $ckOptions = [
            'filebrowserBrowseUrl' => $this->router->getUrl('mediabrowser'),
            'filebrowserWindowWidth' => 1024,
            'filebrowserWindowHeight' => 600,
        ];
        $form = new Form();
        $form->addInput(['pageAdmin', 'title'], new TextInput('title', $record->get('title')));
        $form->addInput(['pageAdmin', 'content'], new CkEditorInput('content', $record->get('content'), $ckOptions));
        return $form;
    }

}