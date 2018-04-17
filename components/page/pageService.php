<?php

class PageService {
    
    private $table;
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->table = $im->get('pageTable');
    }
    
    public function findByLocaleAndName($locale, $name) {
        return $this->table->findOne(null, [
            'where' => [
                ['locale', '=', $locale],
                ['name', '=', $name]
            ]
        ]);
    }
    
}
