<?php

class UserPermissionTable extends Table {

    public function __construct() {
        parent::__construct();
        $this->name = 'user_permission';
        $this->addColumn(new IntegerColumn($this, 'user_id'), null, true);
        $this->addColumn(new IntegerColumn($this, 'permission_id'), null, true);
    }

}