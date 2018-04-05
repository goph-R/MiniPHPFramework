<?php

abstract class AdminForm extends Form {
    
    /**
     * @var Record
     */
    protected $record;
    
    public function __construct(Record $record) {
        parent::__construct();
        $this->record = $record;
    }
    
    abstract public function save();
    
}
