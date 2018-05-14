<?php

$im = InstanceManager::getInstance();

$im->add('postTableFactory', 'PostTableFactory');
$im->add('postService', 'PostService');

$router = $im->get('router');
$router->add('posts', 'PostController', 'index');
$router->add('post/:id', 'PostController', 'view');

$view = $im->get('view');
$view->addPath('post', 'components/post/templates');