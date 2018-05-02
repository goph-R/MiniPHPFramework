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
$router->add('mediabrowser', 'MediaBrowserController', 'index');
$router->add('mediabrowser/folders/:id', 'MediaBrowserController', 'folders');
$view->addPath('browser', 'components/admin/mediaBrowser/templates');


// add settings
$router->add('admin/settings', 'AdminSettingsController', 'index');

$confirmScript = $im->get('confirmScript');
$confirmScript->add();