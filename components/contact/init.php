<?php

$im = InstanceManager::getInstance();
$im->add('contactFormFactory', 'ContactFormFactory');
$im->add('contactService', 'ContactService');
$im->add('contactTableFactory', 'ContactTableFactory');

$router = $im->get('router');
$router->add('contact', 'ContactController', 'index');

$translation = $im->get('translation');
$translation->add('contact', 'components/contact/translations');

$view = $im->get('view');
$view->addPath('contact', 'components/contact/templates');