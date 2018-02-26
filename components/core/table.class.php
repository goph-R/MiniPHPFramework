<?php

abstract class Table {

	protected $config;
	protected $name = null;
	protected $columns = [];
	protected $primaryKeys = [];
	protected $db;

	public function __construct($im) {
		$this->db = $im->get('db');
	}

	public function addColumn($column, $defaultValue = null, $isPrimaryKey = false) {
	    $column->setDefaultValue($defaultValue);
		$this->columns[$column->getName()] = $column;
		if ($isPrimaryKey) {
			$this->primaryKeys[] = $column->getName();
		}
	}

	public function getColumn($name) {
		return isset($this->columns[$name]) ? $this->columns[$name] : null;
	}

	protected function preSave($record) {}
	protected function postSave($record) {}

	public function save($record) {
		$this->preSave($record);
		if ($record->isNew()) {
			$this->insert($record);
		} else {
			$this->update($record);
		}
		$this->postSave($record);
		$record->setNew(false);
	}

	private function escapeName($name) {
		// TODO: true escape
		return '`'.$name.'`';
	}

	private function escapeValue($value) {
		if ($value === null) {
			return 'NULL';
		}
		$ret = $this->db->escape($value);
		if (!is_numeric($value)) {
			$ret = '"'.$value.'"';
		}
		return $ret;
	}

	private function checkOperator($op) {
		$ret = $op;
		if (!in_array($op, ['<', '>', '<=', '>=', '='])) {
			throw new DBException('Unknown operator: '.$op);
		}
		return $ret;
	}

	private function createCondition($condition, $op = 'AND') {		
		$subConditions = [];
		foreach ($condition as $item) {
			if ($item[0] == 'and') {
				$subCondition = $this->createCondition($item[1], 'AND');
			} else if ($item[0] == 'or') {
				$subCondition = $this->createCondition($item[1], 'OR');
			} else if ($item[0] == 'in') {
				$values = [];
				foreach ($item[1] as $value) {
					$values[] = $this->escapeValue($value);
				}
				$subCondition = $this->escapeName($item[0]).' IN ('.join($values, ', ').')';
			} else {
				$subCondition = $this->escapeName($item[0]).' '.$this->checkOperator($item[1]).' '.$this->escapeValue($item[2]);
			}
			$subConditions[] = $subCondition;
		}
		$ret = join($subConditions, ' '.$op.' ');
		return $ret ? '('.$ret.')' : '';
	}

	private function createOrder($query) {
		if (!isset($query['order'])) {
            return '';
        }
        $orders = $query['order'];
        if (!is_array($orders)) {
            $orders = [$orders => 'asc'];
        }
        $sqlOrders = [];
        foreach ($orders as $orderName => $orderDir) {
            $orderDir = $orderDir == 'asc' ? 'asc' : 'desc';
            $sqlOrders[] = $this->escapeName($orderName).' '.$orderDir;
        }
		return 'ORDER BY '.join(', ', $sqlOrders);
	}

	private function createLimit($query) {
		if (!isset($query['limit'])) {
            return '';
        }
        if (is_array($query['limit'])) {
            $sql = ' LIMIT '.(int)$query['limit'][0].', '.(int)$query['limit'][1];
        } else {
            $sql = ' LIMIT '.(int)$query['limit'];
        }
		return $sql;
	}

	private function createSelect($columnNames, $query) {
		$sql = 'SELECT ';
		if ($columnNames !== null && !is_array($columnNames)) {
			$sql .= $columnNames;
		} else {
			if (!$columnNames) {
				$columnNames = array_keys($this->columns);
			} 
			$escapedColumnNames = [];
			foreach ($columnNames as $name) {
				$escapedColumnNames[] = $this->escapeName($name);
			}
			$sql .= join($escapedColumnNames, ', ');
		}
		$sql .= ' FROM '.$this->escapeName($this->name);
		// TODO: createJoins
		if (array_key_exists('where', $query)) {
			$sql .= ' WHERE ';
			$sql .= $this->createCondition($query['where']);
		}
		// TODO: createGroupBy
		$sql .= $this->createOrder($query);
		$sql .= $this->createLimit($query);
		return $sql;
	}

	public function find($columnNames, $query) {		
		$sql = $this->createSelect($columnNames, $query);
		$result = $this->db->query($sql);
		$ret = [];
		while ($row = $result->fetch()) {
			$record = new Record($this);
			$record->setNew(false);
			foreach ($row as $name => $value) {
				$record->set($name, $value, false);
			}
			$ret[] = $record;
		}
		$result->close();
		return $ret;
	}

	public function findOne($columns, $query) {
		$query['limit'] = 1;
		$ret = $this->find($columns, $query);
		if ($ret) {
			return $ret[0];
		}
		return null;
	}

	public function count($query) {
		if (isset($order['order'])) {
			unset($query['order']);
		}
		if (isset($query['limit'])) {
			unset($query['limit']);
		}
		$sql = $this->createSelect('COUNT(1) as c', $query);
		$result = $this->db->query($sql);
		$ret = $result->fetch();
		$result->close();
		return $ret['c'];
	}

	private function insert($record) {
		$values = [];
		$names = [];
		$autoIncrement = null;
		foreach ($this->columns as $name => $column) {
			$names[] = $this->escapeName($name);
            $value = $record->get($name);
			$values[] = $this->escapeValue($value);
			if ($column->isAutoIncrement() && $value === null) {
				$autoIncrement = $name;
			}
		}
		$sql = 'INSERT INTO '.$this->escapeName($this->name).' (';
		$sql .= join($names, ', ');
		$sql .= ') VALUES (';
		$sql .= join($values, ', ');
		$sql .= ')';
        print $sql;
		$this->db->query($sql);
		if ($autoIncrement) {
			$record->set($autoIncrement, $this->db->lastId(), false);
		}
		$record->setNew(false);
	}

	private function getConditionForPrimaryKeys($record) {
		$pks = [];
		foreach ($this->primaryKeys as $pk) {
			$pks[] = $this->escapeName($pk).' = '.$this->escapeValue($record->get($pk));
		}
		return join($pks, ' AND ');
	}

	private function update($record) {
		$sets = [];
		foreach ($record->getModified() as $name) {
			$sets[] = $this->escapeName($name).' = '.$this->escapeValue($record->get($name));
		}
		$sql = 'UPDATE '.$this->escapeName($this->name);
		$sql .= ' SET '.join($sets, ', ');
		$sql .= ' WHERE '.$this->getConditionForPrimaryKeys($record);
		$sql .= ' LIMIT 1';
		$this->db->query($sql);
		$record->clearModified();
	}

	public function delete($record) {
		$sql = 'DELETE FROM '.$this->escapeName($this->name);
		$sql .= ' WHERE '.$this->getConditionForPrimaryKeys($record);
		$sql .= ' LIMIT 1';
		$this->db->query($sql);
		$record->setNew(true);
	}

}
