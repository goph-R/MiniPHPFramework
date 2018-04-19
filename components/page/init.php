<?php

$im = InstanceManager::getInstance();

$im->add('pageTableFactory', 'PageTableFactory');
$im->add('pageService', 'PageService');

$router = $im->get('router');
$router->add('page/:name', 'PageController', 'index');

$view = $im->get('view');        
$view->addPath('page', 'components/page/templates');        
