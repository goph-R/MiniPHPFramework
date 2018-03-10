<?php

class AdminComponent implements Initable {
    
    /**
     * @var Router
     */
    private $router;

    public function __construct($im) {
        $this->router = $im->get('router');
        $view = $im->get('view');
        $view->addPath('admin', 'components/admin/templates');
    }

    public function init() {
        $this->router->add('admin', 'AdminController', 'index');
    }

}