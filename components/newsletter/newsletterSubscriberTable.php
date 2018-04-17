<?php

class NewsletterSubscriberTable extends Table {
    
    public function __construct() {
        parent::__construct();
        $this->name = 'newsletter_subscriber';
        $this->addColumn(new IntegerColumn($this, 'id'), null, true, true);
        $this->addColumn(new StringColumn($this, 'email', 255));
        $this->addColumn(new BooleanColumn($this, 'active'), 0);
    }    
    
}
