<?php

class PermissionTable extends Table {

    public function __construct($im) {
        parent::__construct($im);
        $this->name = 'permission';
        $this->addColumn(new IntegerColumn($this, 'id'), null, true);
        $this->addColumn(new StringColumn($this, 'name', 50));
    }

}