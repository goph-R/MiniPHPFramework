<?php

class AdminComponent implements Initable {
    
    /**
     * @var Router
     */
    private $router;

    public function __construct(InstanceManager $im) {
        $this->router = $im->get('router');
        $view = $im->get('view');
        $view->addPath('admin', 'components/admin/templates');
        $adminMenu = new AdminMenu();
        $im->add('adminMenu', $adminMenu);
    }

    public function init() {
        $this->router->add('admin', 'AdminController', 'index');
    }

}