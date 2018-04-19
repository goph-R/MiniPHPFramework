<?php

class NewsletterService {
    
    /**
     * @var NewsletterSubscriberTable
     */
    private $table;
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->table = $im->get('newsletterSubscriberTable');
    }
    
    /**
     * @param type $email
     * @return Record
     */
    public function findByEmail($email) {
        return $this->table->findOne(null, [
            'where' => [
                ['email', '=', $email]
            ]
        ]);
    }
    
    public function subscribe($email) {        
        $record = $this->findByEmail($email);
        if (!$record) {
            $record = new Record($this->table);
            $record->set('email', $email);
        }
        $record->set('active', 1);
        $record->save();
    }
    
    public function isSubscribed($email) {
        $record = $this->findByEmail($email);
        return $this->isSubscribedRecord($record);
    }
    
    private function isSubscribedRecord($record) {
        return $record && $record->get('active');
    }

    public function unsubscribe($email) {
        $record = $this->findByEmail($email);
        if ($this->isSubscribedRecord($record)) {
            $record->set('active', 0);
            $record->save();
        }
    }
    
    public function changeEmail($email, $newEmail) {
        $record = $this->findByEmail($email);
        if ($record) {
            $record->set('email', $newEmail);
            $record->save();
        }
    }
    
}
