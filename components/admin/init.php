<?php

$im = InstanceManager::getInstance();

$im->add('confirmScript', 'ConfirmScript');
$im->add('adminMenu', 'AdminMenu');

$view = $im->get('view');
$view->addPath('admin', 'components/admin/templates');
$view->addPath(':core/formError', 'components/admin/templates/formError');

$translation = $im->get('translation');
$translation->add('admin', 'components/admin/translations');

// add media browser
$im->add('mediaTableFactory', 'MediaTableFactory');
$im->add('mediaService', 'mediaService');
$router->add('mediabrowser', 'MediaBrowserController', 'index');
$view->addPath('browser', 'components/admin/mediabrowser/templates');