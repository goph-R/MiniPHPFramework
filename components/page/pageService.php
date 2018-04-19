<?php

class PageService {
    
    private $table;
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $tableFactory = $im->get('pageTableFactory');
        $this->table = $tableFactory->createPage();
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
