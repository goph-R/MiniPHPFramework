<?php

class UserDeleteConfirmActionButton extends ConfirmActionButton {
    
    public function fetch(Record $record, $params=[]) {
        $im = InstanceManager::getInstance();
        $user = $im->get('user');
        if ($user->get('id') == $record->get('id')) {
            return '';
        }
        return parent::fetch($record, $params);
    }
    
}