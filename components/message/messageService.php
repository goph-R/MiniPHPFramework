<?php

class MessageService {
    
    /**
     * @var Table
     */
    protected $userMessageTable;
    
    /**
     * @var Table
     */
    protected $messageTable;

    /**
     * @var User
     */
    protected $user;
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->user = $im->get('user');
        $tableFactory = $im->get('messageTableFactory');
        $this->userMessageTable = $tableFactory->createUserMessage();
        $this->messageTable = $tableFactory->createMessage();
    }
    
    protected function getColumnNames() {
        return [
            'message_user.id',
            'message_user.user_id',
            'message_user.message_id',
            'message_user.read',
            'message.created_on',
            'message.subject',
            'message.text',
            'message.recipient_id',
            'message.sender_id'
        ];
    }

    public function isOwned(Record $message) {
        return $message->get('user_id') == $this->user->get('id');
    }

    protected function getMessageJoin() {
        return [[
            'table' => 'message',
            'on'    => [['message.id', '=', ['message_id']]]
        ]];
    }
    
    public function findById($id) {        
        return $this->userMessageTable->findOne($this->getColumnNames(), [
            'where' => [['message_user.id', '=', $id]],
            'join'  => $this->getMessageJoin()
        ]);
    }

    public function findCountByUserIdAndSent($userId, $sent) {
        return $this->userMessageTable->count(
            $this->getQueryForUserIdAndSent($userId, $sent, null)
        );
    }
    
    public function findAllByUserIdAndSent($userId, $sent, $limit=[]) {
        return $this->userMessageTable->find(
            $this->getColumnNames(),
            $this->getQueryForUserIdAndSent($userId, $sent, $limit)
        );
    }

    protected function getQueryForUserIdAndSent($userId, $sent, $limit) {
        $userIdColumn = $sent ? 'sender_id' : 'recipient_id';
        return [
            'where' => [
                ['user_id', '=', $userId],
                [$userIdColumn, '=', $userId]
            ],
            'join'  => $this->getMessageJoin(),
            'order' => ['id' => 'desc'],
            'limit' => $limit
        ];
    }
    
    public function send($senderId, $recipientId, $replyTo, $values) {
        $message = $this->messageTable->insert(
            $this->getMessageValues($senderId, $recipientId, $replyTo, $values)
        );
        $messageId = $message->get('id');
        $this->userMessageTable->insert([
            'message_id' => $messageId,
            'user_id'    => $senderId,
            'read'       => true
        ]);
        $this->userMessageTable->insert([
            'message_id' => $messageId,
            'user_id'    => $recipientId
        ]);
    }

    protected function getMessageValues($senderId, $recipientId, $replyTo, $values) {
        return [
            'created_on'   => time(),
            'reply_to'     => $replyTo,
            'sender_id'    => $senderId,
            'recipient_id' => $recipientId,
            'subject'      => $values['subject'],
            'text'         => $values['text']
        ];
    }

    public function createReplyText($text) {
        $parts = explode("\n", $text);
        return "\n\n\n> ".join("\n> ", $parts);
    }

    public function createReplyTitle($title) {
        return mb_substr($title, 0, 3) == 'RE:' ? $title : 'RE: '.$title;
    }

    public function formatTimestamp($timestamp) {
        return date('Y-m-d H:i', $timestamp);
    }
    
    public function formatText($text) {
        return nl2br(htmlspecialchars($text));
    }
    
    
}

