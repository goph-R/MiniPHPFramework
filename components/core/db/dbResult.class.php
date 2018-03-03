<?php

class DBResult {

    /**
     * @var mysqli_result
     */
    private $result;

    public function __construct($result) {
        $this->result = $result;
    }

    public function count() {
        return $this->result->num_rows;
    }

    public function fetch() {
        return $this->result->fetch_assoc();
    }

    public function close() {
        $this->result->free();
    }

}
