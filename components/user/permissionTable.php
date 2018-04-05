<?php

class PermissionTable extends Table {

    public function __construct() {
        parent::__construct();
        $this->name = 'permission';
        $this->addColumn(new IntegerColumn($this, 'id'), null, true);
        $this->addColumn(new StringColumn($this, 'name', 50));
    }

}