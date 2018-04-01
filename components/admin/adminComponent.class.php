<?php

class AdminComponent implements Initiable {
    
    /**
     * @var Router
     */
    private $router;

    public function __construct() {
        $im = InstanceManager::getInstance();
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