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

    private function getWhereByLocale($locale) {
        return [
            ['locale', '=', $locale],
            ['active', '=', true]
        ];
    }

    public function findActiveByLocale($locale, $limit=[]) {
        return $this->table->find(null, [
            'where' => $this->getWhereByLocale($locale),
            'order' => ['created_on' => 'desc'],
            'limit' => $limit
        ]);
    }

    public function findActiveCountByLocale($locale) {
        return $this->table->count([
            'where' => $this->getWhereByLocale($locale)
        ]);
    }

    public function formatDate($time) {
        return date('Y-m-d H:i', $time);
    }

    public function findActiveById($id) {
        return $this->table->findOne(null, [
            'where' => [
                ['id', '=', $id],
                ['active', '=', true]
            ]
        ]);
    }

}