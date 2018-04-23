<?php

$im = InstanceManager::getInstance();

$im->add('postTableFactory', 'PostTableFactory');
$im->add('postService', 'PostService');