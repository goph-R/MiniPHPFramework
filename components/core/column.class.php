<?php

abstract class Column {

	protected $name;
	protected $table;
	protected $autoIncrement;
	// TODO: current_timestamp

	public function __construct($table, $name, $autoIncrement = false) {
		$this->table = $table;
		$this->name = $name;
		$this->autoIncrement = $autoIncrement;
	}

	public function getName() {
		return $this->name;
	}

	public function isAutoIncrement() {
		return $this->autoIncrement;
	}

	public function convert($value) {
		return $value;
	}
}
