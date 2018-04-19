<?php

class UserTableFactory {

    /**
     * @return Table
     */
    public function createUser() {
        $table = new Table('user');
        $table->addColumn(new IntegerColumn($table, 'id'), null, true, true);
        $table->addColumn(new StringColumn($table, 'email', 255));
        $table->addColumn(new StringColumn($table, 'password', 255));
        $table->addColumn(new IntegerColumn($table, 'last_login'), 0);
        $table->addColumn(new BooleanColumn($table, 'active'), 0);
        $table->addColumn(new StringColumn($table, 'activation_hash', 32), null);
        $table->addColumn(new StringColumn($table, 'forgot_hash', 32), null);
        $table->addColumn(new StringColumn($table, 'remember_hash', 32), null);
        $table->addColumn(new StringColumn($table, 'new_email', 255), null);
        $table->addColumn(new StringColumn($table, 'new_email_hash', 32), null);
        return $table;
    }

    /**
     * @return Table
     */
    public function createPermission() {
        $table = new Table('permission');
        $table->addColumn(new IntegerColumn($table, 'id'), null, true);
        $table->addColumn(new StringColumn($table, 'name', 50));
        return $table;
    }

    /**
     * @return Table
     */
    public function createUserPermission() {
        $table = new Table('user_permission');
        $table->addColumn(new IntegerColumn($table, 'user_id'), null, true);
        $table->addColumn(new IntegerColumn($table, 'permission_id'), null, true);
        return $table;
    }

}