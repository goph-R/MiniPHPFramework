<?php

class MediaService {
    
    /**
     * @var Table
     */
    private $table;

    const TYPE_FOLDER = 1;
    const TYPE_FILE = 2;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $tableFactory = $im->get('mediaTableFactory');
        $this->table = $tableFactory->createMedia();
    }
    
}
