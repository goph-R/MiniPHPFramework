<?php

class ContactTableFactory {


    /**
     * @return Table
     */

    public function createContact() {
        $table = new Table('contact');
        $table->addColumn(new IntegerColumn('id'), null, true, true);
        $table->addColumn(new IntegerColumn('created_on'));
        $table->addColumn(new StringColumn('name', 255));
        $table->addColumn(new StringColumn('email', 255));
        $table->addColumn(new StringColumn('message'));
        return $table;
    }

}