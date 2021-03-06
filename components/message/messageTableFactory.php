<?php

class MessageTableFactory {

    /**
     * @return Table
     */
    public function createMessage() {
        $table = new Table('message');
        $table->addColumn(new IntegerColumn('id'), null, true, true);
        $table->addColumn(new IntegerColumn('reply_to'));
        $table->addColumn(new IntegerColumn('created_on'));
        $table->addColumn(new IntegerColumn('sender_id'));
        $table->addColumn(new IntegerColumn('recipient_id'));
        $table->addColumn(new StringColumn('subject', 255));
        $table->addColumn(new StringColumn('text'));
        return $table;
    }

    /**
     * @return Table
     */
    public function createUserMessage() {
        $table = new Table('message_user');
        $table->addColumn(new IntegerColumn('id'), null, true, true);
        $table->addColumn(new IntegerColumn('message_id'));
        $table->addColumn(new IntegerColumn('user_id'));
        $table->addColumn(new BooleanColumn('read'), 0);
        return $table;
    }

    
}

