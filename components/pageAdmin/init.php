<?php

$im = InstanceManager::getInstance();

$router = $im->get('router');
$router->add('admin/page', 'PageAdminController', 'index');
$router->add('admin/page/edit', 'PageAdminController', 'edit');

$translation = $im->get('translation');
$translation->add('pageAdmin', 'components/pageAdmin/translations');

$adminMenu = $im->get('adminMenu');
$adminMenu->addItem($translation->get('pageAdmin', 'pages'), 'admin/page', 'file-alt');
