<?php

$im = InstanceManager::getInstance();

$im->add('newsletterSubscriberTable', new NewsletterSubscriberTable());
$im->add('newsletterService', new NewsletterService());
