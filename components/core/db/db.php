<?php

class DB implements Finishable {

    /**
     * @var mysqli
     */
    private $conn;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    private $name;

    public function __construct($name) {
        $im = InstanceManager::getInstance();
        $this->config = $im->get('config');
        $this->logger = $im->get('logger');
        $this->name = $name;
    }

    public function connect() {
        if ($this->conn) {
            return;
        }
        $this->logger->info('Connecting to database "'.$this->name.'"');
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
        $this->logger->info('Execute SQL on "'.$this->name.'":'."\r\n".$sql."\r\n");
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
            $this->logger->info('Closing database "'.$this->name.'" connection');
        }
    }

    public function finish() {
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
