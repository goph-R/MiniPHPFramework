<?php

$im = InstanceManager::getInstance();

$router = $im->get('router');
$view = $im->get('view');
$translation = $im->get('translation');

$im->add('confirmScript', 'ConfirmScript');
$im->add('messageTableFactory', 'MessageTableFactory');
$im->add('messageService', 'MessageService');
$im->add('messageFormFactory', 'MessageFormFactory');

$router->add('messages', 'MessageListController', 'index');
$router->add('messages/sent', 'MessageListController', 'index');
$router->add('message/write/:recipient_id', 'MessageWriteController', 'index');
$router->add('message/reply/:reply_to', 'MessageWriteController', 'reply');
$router->add('message/view/:id', 'MessageViewController', 'index');
$router->add('message/delete/:id', 'MessageDeleteController', 'index');

$view->addPath('message', 'components/message/templates');

$translation->add('message', 'components/message/translations');