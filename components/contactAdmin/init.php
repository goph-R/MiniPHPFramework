<?php

$im = InstanceManager::getInstance();

$router = $im->get('router');
$router->add('admin/contacts', 'ContactAdminController', 'index');
$router->add('admin/contact/view', 'ContactAdminController', 'view');
$router->add('admin/contact/delete', 'ContactAdminController', 'delete');

$translation = $im->get('translation');
$translation->add('contactAdmin', 'components/contactAdmin/translations');

$view = $im->get('view');
$view->addPath('contactAdmin', 'components/contactAdmin/templates');

$adminMenu = $im->get('adminMenu');
$adminMenu->addItem($translation->get('contactAdmin', 'contacts'), 'admin/contacts', 'at');