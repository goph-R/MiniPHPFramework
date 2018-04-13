<?php

class UserAdminEmailExistsValidator extends Validator {

    /**
     * @var UserTable
     */
    private $userTable;
    
    /**
     * @var Record
     */
    private $exceptRecord;

    public function __construct($exceptRecord=null) {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->message = $this->translation->get('user', 'email_exists');
        $this->userTable = $im->get('userTable');
        $this->exceptRecord = $exceptRecord;
    }

    public function doValidate($value) {
        $where = [];
        $where[] = ['not', $this->userTable->getConditionsForRecord($this->exceptRecord)];
        $where[] = ['email', '=', $value];
        if ($this->userTable->findOne(null, ['where' => $where])) {
            return false;
        }
        return true;
    }

}