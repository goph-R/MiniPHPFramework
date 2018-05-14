<?php

class PostService {

    /**
     * @var Table
     */
    private $table;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $tableFactory = $im->get('postTableFactory');
        $this->table = $tableFactory->createPost();
    }

    public function findActiveByLocale($locale, $limit=[]) {
        return $this->table->find(null, [
            'where' => [
                ['locale', '=', $locale],
                ['active', '=', true]
            ],
            'order' => ['created_on' => 'desc'],
            'limit' => $limit
        ]);
    }

    public function formatDate($time) {
        return date('Y-m-d H:i', $time);
    }

    public function findActiveById($id) {
        return $this->table->find(null, [
            'where' => [
                ['id', '=', $id],
                ['active', '=', true]
            ]
        ]);
    }

}