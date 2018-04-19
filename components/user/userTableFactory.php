<?php

class UserTableFactory {

    /**
     * @return Table
     */
    public function createUser() {
        $table = new Table('user');
        $table->addColumn(new IntegerColumn('id'), null, true, true);
        $table->addColumn(new StringColumn('email', 255));
        $table->addColumn(new StringColumn('password', 255));
        $table->addColumn(new IntegerColumn('last_login'), 0);
        $table->addColumn(new BooleanColumn('active'), 0);
        $table->addColumn(new StringColumn('activation_hash', 32), null);
        $table->addColumn(new StringColumn('forgot_hash', 32), null);
        $table->addColumn(new StringColumn('remember_hash', 32), null);
        $table->addColumn(new StringColumn('new_email', 255), null);
        $table->addColumn(new StringColumn('new_email_hash', 32), null);
        return $table;
    }

    /**
     * @return Table
     */
    public function createPermission() {
        $table = new Table('permission');
        $table->addColumn(new IntegerColumn('id'), null, true);
        $table->addColumn(new StringColumn('name', 50));
        return $table;
    }

    /**
     * @return Table
     */
    public function createUserPermission() {
        $table = new Table('user_permission');
        $table->addColumn(new IntegerColumn('user_id'), null, true);
        $table->addColumn(new IntegerColumn('permission_id'), null, true);
        return $table;
    }

}