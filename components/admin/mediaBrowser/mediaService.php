<?php

class MediaService {
    
    /**
     * @var Table
     */
    private $table;
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $tableFactory = $im->get('mediaTableFactory');
        $this->table = $tableFactory->createMedia();
    }
    
}
