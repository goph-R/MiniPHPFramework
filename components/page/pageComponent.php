<?php

class PageComponent {
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $router = $im->get('router');
        $view = $im->get('view');        
        $im->add('pageTable', new PageTable());
        $im->add('pageService', new PageService());
        $router->add('page/:name', 'PageController', 'index');
        $view->addPath('page', 'components/page/templates');
    }
    
}
