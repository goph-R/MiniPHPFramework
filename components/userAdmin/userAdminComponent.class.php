<?php

class UserAdminComponent implements Initable {

    /**
     * @var Router
     */
    private $router;

    /**
     * @var AdminMenu
     */
    private $adminMenu;

    public function __construct(InstanceManager $im) {
        $this->router = $im->get('router');
        $this->adminMenu = $im->get('adminMenu');
    }

    public function init() {
        $this->adminMenu->addItem('Felhasználók', 'admin/users', 'users');
        $this->adminMenu->addItem('Kereskedők', 'admin/companies', 'building');
        $this->adminMenu->addItem('Járművek', 'admin/vehicles', 'car');
    }

}