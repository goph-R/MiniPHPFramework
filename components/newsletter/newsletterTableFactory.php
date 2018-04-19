<?php

class NewsletterTableFactory {

    /**
     * @return Table
     */
    public function createSubscriber() {
        $table = new Table('newsletter_subscriber');
        $table->addColumn(new IntegerColumn('id'), null, true, true);
        $table->addColumn(new StringColumn('email', 255));
        $table->addColumn(new BooleanColumn('active'), 0);
        return $table;
    }    
    
}
