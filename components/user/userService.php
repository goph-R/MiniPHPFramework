<?php

class UserService {

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var Translation
     */
    protected $translation;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var InstanceManager
     */
    protected $im;

    /**
     * @var Table
     */
    protected $userTable;

    /**
     * @var Table
     */
    protected $permissionTable;

    /**
     * @var Table
     */
    protected $userPermissionTable;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->config = $im->get('config');
        $this->user = $im->get('user');
        $this->mailer = $im->get('mailer');
        $this->translation = $im->get('translation');
        $this->request = $im->get('request');
        $userTableFactory = $im->get('userTableFactory');
        $this->userTable = $userTableFactory->createUser();
        $this->permissionTable = $userTableFactory->createPermission();
        $this->userPermissionTable = $userTableFactory->createUserPermission();
        $this->rememberLogin();
    }

    /**
     * @return Record
     */
    public function createRecord() {
        return new Record($this->userTable);
    }

    public function hash($value) {
        return md5($this->config->get('user.salt').$value);
    }

    public function rememberLogin() {
        $rememberHash = $this->request->getCookie('remember_hash');
        if (!$this->user->isLoggedIn() && $rememberHash) {
            $record = $this->findActiveByRememberHash($rememberHash);
            if ($record) {
                $this->doLogin($record);
            }
        }
    }

    /**
     * @param $email
     * @param $password
     * @return Record
     */
    private function findActiveByEmailAndPassword($email, $password) {
        $record = $this->userTable->findOne(null, [
            'where' => [
                ['email', '=', $email],
                ['password', '=', $this->hash($password)],
                ['active', '=', 1]
            ]
        ]);
        return $record;
    }

    /**
     * @param $id
     * @return Record
     */
    public function findById($id) {
        return $this->userTable->findOne(null, [
            'where' => [
                ['id', '=', $id]
            ]
        ]);
    }

    /**
     * @param $email
     * @return Record
     */
    public function findByEmail($email) {
        return $this->userTable->findOne(null, [
            'where' => [
                ['email', '=', $email]
            ]
        ]);
    }

    /**
     * @param $hash
     * @return Record
     */
    public function findByActivationHash($hash) {
        return $this->userTable->findOne(null, [
            'where' => [
                ['activation_hash', '=', $hash]
            ]
        ]);
    }

    /**
     * @param $hash
     * @return Record
     */
    public function findByForgotHash($hash) {
        return $this->userTable->findOne(null, [
            'where' => [
                ['forgot_hash', '=', $hash]
            ]
        ]);
    }

    /**
     * @param $hash
     * @return Record
     */
    public function findActiveByRememberHash($hash) {
        return $this->userTable->findOne(null, [
            'where' => [
                ['remember_hash', '=', $hash],
                ['active', '=', 1]
            ]
        ]);
    }

    public function findPermissionNamesByIds($ids) {
        return $this->permissionTable->findColumn('name', [
            'where' => [
                ['id', 'in', $ids]
            ]
        ]);
    }

    public function findPermissionIdsByUserId($userId) {
        return $this->userPermissionTable->findColumn('permission_id', [
            'where' => [
                ['user_id', '=', $userId]
            ]
        ]);
    }

    public function emailExistsExcept($email, Record $exceptRecord) {
        $where = [];
        $where[] = ['not', $this->userTable->getConditionsForRecord($exceptRecord)];
        $where[] = ['email', '=', $email];
        if ($this->userTable->findOne(null, ['where' => $where])) {
            return false;
        }
        return true;
    }

    public function login($email, $password, $remember) {
        $record = $this->findActiveByEmailAndPassword($email, $password);
        if (!$record) {
            return false;
        }
        if ($remember) {
            $hash = $this->hash(time());
            $record->set('remember_hash', $hash);
            $this->request->setCookie('remember_hash', $hash);
        }
        $this->doLogin($record);
        return true;
    }

    private function doLogin(Record $record) {        
        $record->set('last_login', time());
        $record->save();
        foreach ($record->getAttributes() as $name => $value) {
            $this->user->set($name, $value);
        }
        $permissionIds = $this->findPermissionIdsByUserId($record->get('id'));
        $permissions = $this->findPermissionNamesByIds($permissionIds);
        foreach ($permissions as $permission) {
            $this->user->addPermission($permission);
        }
        $this->user->setLoggedIn(true);
    }

    public function logout() {
        $id = $this->user->get('id');
        $record = $this->findById($id);
        if ($record) {
            $record->set('remember_hash', null);
            $record->save();
        }
        $this->request->setCookie('remember_hash', null);
        $this->user->destroy();
    }

    public function register($values) {
        $fields = ['email'];
        $hash = $this->hash(time());
        $record = $this->createRecord();
        $record->setAll($fields, $values);
        $record->set('password', $this->hash($values['password']));
        $record->set('activation_hash', $hash);
        $record->save();
        return $record;
    }

    public function sendRegisterEmail($values, $hash) {
        if (!isset($values['email'])) {
            throw new Exception('There is no email in the values.');
        }
        $this->mailer->init();        
        $this->mailer->addAddress($values['email']);
        foreach ($values as $name => $value) {
            $this->mailer->set($name, $value);
        }
        $this->mailer->set('hash', $hash);
        return $this->mailer->send(
            $this->translation->get('user', 'registration'),
            ':user/registerEmail'
        );
    }

    public function activate($hash) {
        $record = $this->findByActivationHash($hash);
        if (!$record) {
            return false;
        }
        $record->set('activation_hash', null);
        $record->set('active', 1);
        $record->save();
        return true;
    }

    public function sendForgotEmail($email) {
        $record = $this->findByEmail($email);
        if (!$record) {
            return false;
        }
        $hash = $this->hash(time());
        $record->set('forgot_hash', $hash);
        $record->save();
        $this->mailer->init();
        $this->mailer->addAddress($email);
        $this->mailer->set('hash', $hash);
        $result = $this->mailer->send(
            $this->translation->get('user', 'password_changing'),
            ':user/forgotEmail'
        );
        return $result;
    }

    public function changeForgotPassword(Record $record, $password) {
        $record->set('forgot_hash', '');
        $record->set('password', $this->hash($password));
        $record->save();
    }
    
    public function changePassword($id, $password) {
        $record = $this->findById($id);
        if (!$record) {
            return false;
        }
        $passwordHash = $this->hash($password);
        $record->set('password', $passwordHash);
        $record->save();
        if ($id == $this->user->get('id')) {
            $this->user->set('password', $passwordHash);
        }
        return true;
    }
    
    public function saveNewEmail($id, $email) {
        $record = $this->findById($id);
        if (!$record) {
            return false;
        }
        $hash = $this->hash($email);
        $record->set('new_email', $email);
        $record->set('new_email_hash', $hash);
        $record->save();
        if ($id == $this->user->get('id')) {
            $this->user->set('new_email', $email);
            $this->user->set('new_email_hash', $hash);
        }
        return true;
    }
    
    public function sendNewAddressEmail($email, $id, $hash) {
        $this->mailer->init();
        $this->mailer->addAddress($email);
        $this->mailer->set('id', $id);
        $this->mailer->set('hash', $hash);
        $result = $this->mailer->send(
            $this->translation->get('user', 'new_email_address'),
            ':user/newAddressEmail'
        );
        return $result;
    }
    
    public function activateNewEmail($id, $hash) {
        $record = $this->findById($id);
        if (!$record || $record->get('new_email_hash') != $hash) {
            return false;
        }
        $email = $record->get('new_email');
        $record->set('new_email', null);
        $record->set('new_email_hash', null);
        $record->set('email', $email);        
        $record->save();
        if ($id == $this->user->get('id')) {
            $this->user->set('new_email', null);
            $this->user->set('new_email_hash', null);
            $this->user->set('email', $email);
        }
        return true;
    }


}