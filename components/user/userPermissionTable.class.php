<?php

class UserPermissionTable extends Table {

    public function __construct($im) {
        parent::__construct($im);
        $this->name = 'user_permission';
        $this->addColumn(new IntegerColumn($this, 'user_id'), null, true);
        $this->addColumn(new IntegerColumn($this, 'permission_id'), null, true);
    }

}