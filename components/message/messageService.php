<?php

class MessageService {
    
    /**
     * @var Table
     */
    private $userMessageTable;
    
    /**
     * @var Table
     */
    private $messageTable;
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $tableFactory = $im->get('messageTableFactory');
        $this->userMessageTable = $tableFactory->createUserMessage();
        $this->messageTable = $tableFactory->createMessage();
    }
    
    private function getColumnNames() {
        return [
            'message_user.id',
            'message_user.user_id',
            'message_user.message_id',
            'message_user.read',
            'message_user.active',
            'message.created_on',
            'message.text',
            'message.recipient_id',
            'message.sender_id'
        ];
    }
    
    private function getMessageJoin() {
        return [
            'table' => 'message',
            'on' => [
                ['message.id', '=', ['message_id']]
            ]
        ];
    }
    
    public function findById($id) {        
        return $this->userMessageTable->findOne($this->getColumnNames(), [
            'where' => [
                ['message_user.id', '=', $id]
            ],
            'join' => $this->getMessageJoin()
        ]);
    }
    
    public function findAllByUserIdAndSent($userId, $sent, $limit=[]) {
        $userIdColumn = $sent ? 'sender_id' : 'recipient_id';
        return $this->userMessageTable->find($this->getColumnNames(), [
            'where' => [
                ['user_id', '=', $userId],
                [$userIdColumn, '=', $userId]
            ],
            'join' => $this->getMessageJoin(),
            'order' => ['id' => 'desc'],
            'limit' => $limit
        ]);
    }
    
    public function send($fromUserId, $toUserId, $text) {
        $message = $this->messageTable->insert([
            'created_on'   => time(),
            'sender_id'    => $fromUserId,
            'recipient_id' => $toUserId,
            'text'         => $text
        ]);
        $this->userMessageTable->insert([
            'user_id'    => $fromUserId,
            'message_id' => $message->get('id'),
            'read'       => true
        ]);
        $this->userMessageTable->insert([
            'user_id'    => $toUserId,
            'message_id' => $message->get('id')
        ]);
    }

    public function formatTimestamp($timestamp) {
        return date('Y-m-d H:i', $timestamp);
    }
    
    public function formatMessage($message) {
        return nl2br(htmlspecialchars($message->get('text')));
    }
    
    
}

