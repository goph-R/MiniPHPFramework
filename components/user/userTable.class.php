<?php

class UserTable extends Table {

	public function __construct($db) {
		parent::__construct($db);
		$this->name = 'user';
		$this->addColumn(new IntegerColumn($this, 'id', true), true);
		$this->addColumn(new StringColumn($this, 'email', 255));
		$this->addColumn(new StringColumn($this, 'name', 255));
		$this->addColumn(new StringColumn($this, 'password', 255));
		$this->addColumn(new IntegerColumn($this, 'last_login'));
		$this->addColumn(new BooleanColumn($this, 'active'));
	}

}