<?php

class DB implements Doneable {

    /**
     * @var mysqli
     */
    private $conn;

    /**
     * @var Config
     */
    private $config;

    private $name;

    public function __construct(InstanceManager $im, $name) {
        $this->config = $im->get('config');
        $this->name = $name;
    }

    public function connect() {
        if ($this->conn) {
            return;
        }
        $this->conn = new mysqli(
            $this->config->get('db.'.$this->name.'.host'),
            $this->config->get('db.'.$this->name.'.user'),
            $this->config->get('db.'.$this->name.'.password'),
            $this->config->get('db.'.$this->name.'.name')
        );
        if ($this->conn->connect_errno) {
            $message = $this->conn->connect_errno.' '.$this->conn->connect_error;
            $this->conn = null;
            throw new DBException($message);
        }
        $this->query('SET NAMES utf8mb4');
    }

    public function query($sql) {
        $this->connect();
        $result = $this->conn->query($sql);
        if ($result) {
            $ret = new DBResult($result);
            return $ret;
        }
        throw new DBException($this->conn->errno.' '.$this->conn->error);
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function done() {
        $this->close();
    }

    public function escape($string) {
        $this->connect();
        return $this->conn->real_escape_string($string);
    }

    public function lastId() {
        return $this->conn->insert_id;
    }

}
