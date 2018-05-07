<?php

$im = InstanceManager::getInstance();
$router = $im->get('router');

$im->add('mediaTableFactory', 'MediaTableFactory');
$im->add('mediaService', 'mediaService');

$router->add('media/thumbnail/:id', 'MediaController', 'thumbnail');

