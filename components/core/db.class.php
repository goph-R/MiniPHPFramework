<?php

class DB {

    private $config;
    private $connected = false;
    private $conn;
    private $name;

    public function __construct($im) {
        $this->config = $im->get('config');
    }

    public function init() {
        $this->name = $this->config->get('db.config');
    }

    public function connect() {
        if ($this->connected) {
            return;
        }
        $this->conn = new mysqli(
            $this->config->get('db.'.$this->name.'.host'),
            $this->config->get('db.'.$this->name.'.user'),
            $this->config->get('db.'.$this->name.'.password'),
            $this->config->get('db.'.$this->name.'.name')
        );
        if ($this->conn->connect_errno) {
            throw new DBException($this->conn->connect_errno.' '.$this->conn->connect_error);
        }
        $this->connected = true;
        $this->query('SET NAMES utf8mb4');
    }

    public function query($sql) {
        $this->connect();
        $result = $this->conn->query($sql);
        if ($result && $result !== 1) {
            $ret = new DBResult($result);
            return $ret;
        }
        throw new DBException($this->conn->errno.' '.$this->conn->error);
    }

    public function close() {
        if ($this->connected) {
            $this->conn->close();
        }
    }

    public function escape($string) {
        $this->connect();
        return $this->conn->real_escape_string($string);
    }

    public function lastId() {
        return $this->conn->insert_id;
    }

}
