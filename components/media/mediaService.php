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
    
    /**
     * @param int $id
     * @return Record
     */
    public function findFolder($id) {
        return $this->table->findOne(null, [
            'where' => [
                ['id', '=', $id],
                ['type', '=', self::TYPE_FOLDER]
            ]
        ]);        
    }
    
    /**
     * @param int
     * @return Record[]
     */
    public function findFolders($parentId) {
        return $this->table->find(null, [
            'where' => [
                ['parent_id', '=', $parentId],
                ['type', '=', self::TYPE_FOLDER]
            ],
            'order' => ['name' => 'asc']
        ]);
    }
    
}
