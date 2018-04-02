<?php

class UserAdminComponent {

    public function __construct() {
        $im = InstanceManager::getInstance();
        $router = $im->get('router');
        $router->add('admin', 'UserAdminController', 'index');
        $router->add('admin/user/edit', 'UserAdminController', 'edit');
        $router->add('admin/user/delete', 'UserAdminController', 'delete');
        $adminMenu = $im->get('adminMenu');
        $adminMenu->addItem('Felhasználók', 'admin', 'users');
        //$adminMenu->addItem('Kereskedők', 'admin/companies', 'building');
        //$adminMenu->addItem('Járművek', 'admin/vehicles', 'car');
    }

}