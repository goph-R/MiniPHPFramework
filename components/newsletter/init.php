<?php

$im = InstanceManager::getInstance();

$im->add('newsletterTableFactory', 'NewsletterTableFactory');
$im->add('newsletterService', 'NewsletterService');
