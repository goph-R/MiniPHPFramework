<?php

class MediaTableFactory {

    public function createMedia() {
        $table = new Table('media');
        $table->addColumn(new IntegerColumn('id'), null, true, true);
        $table->addColumn(new IntegerColumn('parent_id'));
        $table->addColumn(new IntegerColumn('type'));
        $table->addColumn(new IntegerColumn('user_id'));
        $table->addColumn(new IntegerColumn('created_on'));
        $table->addColumn(new BooleanColumn('deleted'), false);
        $table->addColumn(new StringColumn('name', 255));
        $table->addColumn(new StringColumn('extension', 255));
        $table->addColumn(new StringColumn('hash', 32));
        return $table;
    }
    
}
