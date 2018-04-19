<?php

class UserDeleteConfirmActionButton extends ConfirmActionButton {

    /**
     * @var User
     */
    private $user;

    public function __construct($route, $icon) {
        parent::__construct($route, $icon);
        $im = InstanceManager::getInstance();
        $this->user = $im->get('user');
    }

    public function fetch(Record $record, $params=[]) {
        if ($this->user->get('id') == $record->get('id')) {
            return '';
        }
        return parent::fetch($record, $params);
    }
    
}