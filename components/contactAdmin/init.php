<?php

$im = InstanceManager::getInstance();

$router = $im->get('router');
$router->add('admin/contacts', 'ContactAdminController', 'index');
$router->add('admin/contact/view', 'ContactAdminController', 'edit');

$translation = $im->get('translation');
$translation->add('contactAdmin', 'components/contactAdmin/translations');

$adminMenu = $im->get('adminMenu');
$adminMenu->addItem($translation->get('contactAdmin', 'contacts'), 'admin/contacts', 'at');