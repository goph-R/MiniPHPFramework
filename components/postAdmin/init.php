<?php

$im = InstanceManager::getInstance();

$router = $im->get('router');
$router->add('admin/posts', 'PostAdminController', 'index');
$router->add('admin/post/add', 'PostAdminController', 'add');
$router->add('admin/post/edit', 'PostAdminController', 'edit');
$router->add('admin/post/delete', 'PostAdminController', 'delete');

$translation = $im->get('translation');
$translation->add('postAdmin', 'components/postAdmin/translations');

$adminMenu = $im->get('adminMenu');
$adminMenu->addItem($translation->get('postAdmin', 'posts'), 'admin/posts', 'copy');
