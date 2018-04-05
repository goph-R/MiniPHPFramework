<?php

class UserAdminComponent {

    public function __construct() {
        $im = InstanceManager::getInstance();
        $router = $im->get('router');
        $router->add('admin', 'UserAdminController', 'index');
        $router->add('admin/user/edit', 'UserAdminController', 'edit');
        $router->add('admin/user/delete', 'UserAdminController', 'delete');
        $router->add('admin/user/add', 'UserAdminController', 'add');
        $translation = $im->get('translation');
        $translation->add('userAdmin', 'components/userAdmin/translations');
        $view = $im->get('view');
        $view->addPath('userAdmin', 'components/userAdmin/templates');
        $adminMenu = $im->get('adminMenu');
        $adminMenu->addItem($translation->get('userAdmin', 'users'), 'admin', 'users');

        // Just for fun
        $adminMenu->addItem($translation->get('userAdmin', 'traders'), 'admin/traders', 'building');
        $adminMenu->addItem($translation->get('userAdmin', 'vehicles'), 'admin/vehicles', 'car');
    }

}