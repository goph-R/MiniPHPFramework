<?php

class UserTable extends Table {

    public function __construct() {
        parent::__construct();
        $this->name = 'user';
        $this->addColumn(new IntegerColumn($this, 'id'), null, true, true);
        $this->addColumn(new StringColumn($this, 'email', 255));
        $this->addColumn(new StringColumn($this, 'password', 255));
        $this->addColumn(new IntegerColumn($this, 'last_login'), 0);
        $this->addColumn(new BooleanColumn($this, 'active'), 0);
        $this->addColumn(new StringColumn($this, 'activation_hash', 32));
        $this->addColumn(new StringColumn($this, 'forgot_hash', 32));
        $this->addColumn(new StringColumn($this, 'remember_hash', 32));
    }

}