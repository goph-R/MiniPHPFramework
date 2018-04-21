<?php

$im = InstanceManager::getInstance();

$router = $im->get('router');
$view = $im->get('view');
$translation = $im->get('translation');

$im->add('messageTableFactory', 'MessageTableFactory');
$im->add('messageService', 'MessageService');
$im->add('messageFormFactory', 'MessageFormFactory');

$router->add('messages', 'MessageListController', 'index');
$router->add('message/write/:user_id', 'MessageWriteController', 'index');
$router->add('message/view/:id', 'MessageViewController', 'index');

$view->addPath('message', 'components/message/templates');

$translation->add('message', 'components/message/translations');