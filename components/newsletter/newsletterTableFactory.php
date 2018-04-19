<?php

class NewsletterTableFactory {

    /**
     * @return Table
     */
    public function createSubscriber() {
        $table = new Table('newsletter_subscriber');
        $table->addColumn(new IntegerColumn($table, 'id'), null, true, true);
        $table->addColumn(new StringColumn($table, 'email', 255));
        $table->addColumn(new BooleanColumn($table, 'active'), 0);
        return $table;
    }    
    
}
