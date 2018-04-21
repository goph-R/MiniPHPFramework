<?php

class Table {

    const VALID_OPERATORS = ['<', '>', '<=', '>=', '=', 'like'];
    const VALID_JOIN_TYPES = ['left', 'right', 'inner'];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Column[]
     */
    protected $columns = [];

    /**
     * @var DB
     */
    protected $db;

    protected $primaryKeys = [];
    protected $name = null;

    public function __construct($name) {
        $im = InstanceManager::getInstance();
        $this->db = $im->get('db');
        $this->name = $name;
    }

    public function addColumn(Column $column, $defaultValue=null, $primaryKey=false, $autoIncrement=false) {
        $column->setDefaultValue($defaultValue);
        $column->setAutoIncrement($autoIncrement);
        $this->columns[$column->getName()] = $column;
        if ($primaryKey) {
            $this->primaryKeys[] = $column->getName();
        }
    }

    public function getPrimaryKeys() {
        return $this->primaryKeys;
    }

    public function getColumn($name) {
        return isset($this->columns[$name]) ? $this->columns[$name] : null;
    }
    
    public function getColumns() {
        return $this->columns;
    }

    public function save(Record $record) {
        if ($record->isNew()) {
            $this->dbInsert($record);
        } else {
            $this->dbUpdate($record);
        }
        $record->setNew(false);
    }

    /**
     * @return Record
     */
    public function createRecord($values=null) {
        $record = new Record($this);
        if ($values != null) {
            $record->set($values);
        }
        return $record;
    }
    
    /**
     * @param array $values
     * @return Record
     */
    public function insert($values) {
        $record = $this->createRecord($values);
        $record->save();
        return $record;
    }
    
    private function checkOperator($op) {
        $ret = $op;
        if (!in_array($op, self::VALID_OPERATORS)) {
            throw new DBException('Unknown operator: '.$op);
        }
        return $ret;
    }

    private function createCondition($condition, $op='AND') {
        $subConditions = [];
        foreach ($condition as $item) {
            if ($item[0] == 'and') {
                $subCondition = $this->createCondition($item[1], 'AND');
            } else if ($item[0] == 'or') {
                $subCondition = $this->createCondition($item[1], 'OR');
            } else if ($item[0] == 'not') {
                $subCondition = 'NOT '.$this->createCondition($item[1]);
            } else if ($item[1] == 'in') {
                $subCondition = $this->createInCondition($item);
            } else {
                $subCondition = $this->createGeneralCondition($item);
            }
            $subConditions[] = $subCondition;
        }
        $ret = join($subConditions, ' '.$op.' ');
        return $ret ? '('.$ret.')' : '';
    }
    
    private function createInCondition($item) {
        $values = [];
        foreach ($item[2] as $value) {
            $values[] = $this->escapeValue($value);
        }
        if ($values) {
            $subCondition = $this->escapeName($item[0]).' IN ('.join($values, ', ').')';
        } else {
            $subCondition = 'false';
        }
        return $subCondition;
    }
    
    private function createGeneralCondition($item) {
        if (count($item) != 3) {
            throw new DBException('Invalid general condition: '.json_encode($item));
        }
        $field = $this->escapeName($item[0]);
        $value = $this->escapeValue($item[2]);
        $operator = $this->checkOperator($item[1]);
        if ($operator == '=' && $item[2] === null) {
            $operator = 'IS';
        }
        return $field.' '.$operator.' '.$value;        
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
            $orderDir = $orderDir == 'asc' ? 'ASC' : 'DESC';
            $sqlOrders[] = $this->escapeName($orderName).' '.$orderDir;
        }
        return 'ORDER BY '.join(', ', $sqlOrders);
    }

    private function createLimit($query) {
        if (!isset($query['limit']) || !$query['limit']) {
            return '';
        }
        $limit = $query['limit'];
        if (is_array($limit)) {
            $from = (int)$limit[0];
            $step = (int)$limit[1];
            $sql = ' LIMIT '.$from.', '.$step;
        } else {
            $sql = ' LIMIT '.(int)$limit;
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
        $sql .= ' FROM '.$this->escapeTableName($this->name);
        if (isset($query['join']) && $query['join']) {
            $sql .= $this->createJoins($query['join']);
        }
        if (isset($query['where'])) {
            $condition = $this->createCondition($query['where']);
            if ($condition) {
                $sql .= ' WHERE '.$condition;
            }
        }
        // TODO: createGroupBy
        $sql .= $this->createOrder($query);
        $sql .= $this->createLimit($query);
        return $sql;
    }

    private function checkJoinType($type) {
        $ret = $type;
        if (!in_array($type, self::VALID_JOIN_TYPES)) {
            throw new DBException('Unknown join type: '.$type);
        }
        return $ret;
    }

    private function createJoins($joins) {
        $sql = '';
        if (isset($joins['table'])) {
            $joins = [$joins];
        }
        foreach ($joins as $join) {
            if (!isset($join['table'])) {
                throw new DBException("No table defined for join");
            }
            $type = isset($join['type']) ? $this->checkJoinType($join['type']) : 'left';
            $table = $this->escapeTableName($join['table']);
            $sql .= ' '.strtoupper($type).' JOIN '.$table;
            if (isset($join['on']) && is_array($join['on'])) {
                $sql .= ' ON '.$this->createCondition($join['on'])."\r\n";
            }
        }
        return $sql;
    }

    /**
     * @return Record[]
     */
    public function find($columnNames, $query) {
        $sql = $this->createSelect($columnNames, $query);
        $result = $this->db->query($sql);
        $ret = [];
        while ($row = $result->fetch()) {
            $record = new Record($this);
            $record->setNew(false);
            foreach ($row as $name => $value) {
                $record->setRaw($name, $value);
            }
            $ret[] = $record;
        }
        $result->close();
        return $ret;
    }

    /**
     * @return Record
     */
    public function findOne($columns, $query) {
        $query['limit'] = 1;
        $ret = $this->find($columns, $query);
        if ($ret) {
            return $ret[0];
        }
        return null;
    }

    public function findColumn($columnName, $query) {
        $sql = $this->createSelect($columnName, $query);
        $result = $this->db->query($sql);
        $ret = [];
        do {
            $row = $result->fetch();
            if (isset($row[$columnName])) {
                $ret[] = $row[$columnName];
            }
        }
        while ($row);
        $result->close();
        return $ret;
    }
    
    public function findOneByPrimaryKeys($pkValues) {
        $where = [];
        foreach ($this->getPrimaryKeys() as $pk) {
            $where[] = [$pk, '=', $pkValues[$pk]];
        }
        $record = $this->findOne(null, ['where' => $where]);
        return $record;        
    }
    
    public function getConditionsForRecord($record) {
        $where = [];
        foreach ($this->getPrimaryKeys() as $pk) {
            $where[] = [$pk, '=', $record->get($pk)];
        }
        return $where;
    }

    public function count($query) {
        if (isset($query['order'])) {
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

    private function dbInsert(Record $record) {
        $values = [];
        $names = [];
        $autoIncrement = null;
        foreach ($this->columns as $name => $column) {
            $names[] = $this->escapeName($name);
            $value = $record->getRaw($name);
            $values[] = $this->escapeValue($value);
            if ($column->isAutoIncrement() && $value === null) {
                $autoIncrement = $name;
            }
        }
        $sql = 'INSERT INTO '.$this->escapeTableName($this->name).' (';
        $sql .= join($names, ', ');
        $sql .= ') VALUES (';
        $sql .= join($values, ', ');
        $sql .= ')';
        $this->db->query($sql);
        if ($autoIncrement) {
            $record->set($autoIncrement, $this->db->lastId(), false);
        }
        $record->setNew(false);
    }

    private function dbUpdate(Record $record) {
        if (!$record->getModified()) {
            return;
        }
        $sets = [];
        foreach ($record->getModified() as $name) {
            $sets[] = $this->escapeName($name).' = '.$this->escapeValue($record->getRaw($name));
        }
        $sql = 'UPDATE '.$this->escapeTableName($this->name);
        $sql .= ' SET '.join($sets, ', ');
        $sql .= ' WHERE '.$this->createCondition($this->getConditionsForRecord($record));
        $sql .= ' LIMIT 1';
        $this->db->query($sql);
        $record->clearModified();
    }

    public function delete(Record $record) {
        $sql = 'DELETE FROM '.$this->escapeTableName($this->name);
        $sql .= ' WHERE '.$this->createCondition($this->getConditionsForRecord($record));
        $sql .= ' LIMIT 1';
        $this->db->query($sql);
        $record->setNew(true);
    }
    
    public function escapeTableName($name) {
        return '`'.$name.'`';
    }

    public function escapeName($name) {
        $as = '';
        if (is_array($name)) {
            $keys = array_keys($name);
            $values = array_values($name);
            $name = $keys[0];
            $as = $values[0];
        }
        if (strpos($name, '.') == -1) {
            $name = $this->name.'.'.$name;
        }
        $names = str_replace('.', '`.`', $name);
        $postfix = $as ? ' AS `'.$as.'`' : '';
        return '`'.$names.'`'.$postfix;
    }

    public function escapeValue($value) {
        if ($value === null) {
            return 'NULL';
        }
        if (is_array($value)) {
            return $this->escapeName($value[0]);
        }
        $ret = $this->db->escape($value);
        if (!is_numeric($value)) {
            $ret = '"'.$value.'"';
        }
        return $ret;
    }
    
}
