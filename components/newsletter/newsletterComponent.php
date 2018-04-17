<?php

class NewsletterComponent {
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $im->add('newsletterSubscriberTable', new NewsletterSubscriberTable());
        $im->add('newsletterService', new NewsletterService());
    }
    
}
