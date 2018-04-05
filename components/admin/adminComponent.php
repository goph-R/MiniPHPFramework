<?php

class AdminComponent {
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $view = $im->get('view');
        $view->addPath('admin', 'components/admin/templates');
        $translation = $im->get('translation');
        $translation->add('admin', 'components/admin/translations');
        $adminMenu = new AdminMenu();
        $im->add('adminMenu', $adminMenu);
    }

}