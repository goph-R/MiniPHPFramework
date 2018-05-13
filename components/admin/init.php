<?php

$im = InstanceManager::getInstance();

$im->add('confirmScript', 'ConfirmScript');
$im->add('adminMenu', 'AdminMenu');

$router = $im->get('router');

$view = $im->get('view');
$view->addPath('admin', 'components/admin/templates');
$view->addPath(':core/formError', 'components/admin/templates/formError');

$translation = $im->get('translation');
$translation->add('admin', 'components/admin/translations');

// add media browser
$router->add('mediabrowser', 'MediaBrowserController', 'index');
$router->add('mediabrowser/folders/:id', 'MediaBrowserController', 'folders');
$router->add('mediabrowser/files/:id', 'MediaBrowserController', 'files');
$router->add('mediabrowser/newfolder', 'MediaBrowserController', 'createFolder');
$router->add('mediabrowser/rename', 'MediaBrowserController', 'rename');
$router->add('mediabrowser/delete', 'MediaBrowserController', 'delete');
$router->add('mediabrowser/upload', 'MediaBrowserController', 'upload');
$view->addPath('mediaBrowser', 'components/admin/mediaBrowser/templates');

// add settings
$router->add('admin/settings', 'AdminSettingsController', 'index');
