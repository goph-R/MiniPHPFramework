<?php

$im = InstanceManager::getInstance();

$im->add('pageTable', new PageTable());
$im->add('pageService', new PageService());

$router = $im->get('router');
$router->add('page/:name', 'PageController', 'index');

$view = $im->get('view');        
$view->addPath('page', 'components/page/templates');        
