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

    public function __construct($im) {
        $this->im = $im;
    }

    public function init() {
        $im = $this->im;
        $this->config = $im->get('config');
        $this->user = $im->get('user');
        $this->mailer = $im->get('mailer');
        $this->translation = $im->get('translation');
        $this->request = $im->get('request');
        $this->table = $im->get('userTable');
        $this->rememberLogin();
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
        $record = $this->table->findOne(null, [
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
        return $this->table->findOne(null, [
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
        return $this->table->findOne(null, [
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
        return $this->table->findOne(null, [
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
        return $this->table->findOne(null, [
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
        return $this->table->findOne(null, [
            'where' => [
                ['remember_hash', '=', $hash],
                ['active', '=', 1]
            ]
        ]);
    }

    public function login($email, $password, $remember) {
        $record = $this->findActiveByEmailAndPassword($email, $password);
        if (!$record) {
            return false;
        }
        if ($remember) {
            $hash = md5(time());
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
        $fields = ['email', 'city', 'country', 'zip', 'firstname', 'lastname'];
        $hash = md5(time());
        $record = new Record($this->table);
        $record->setAll($fields, $values);
        $record->set('password', $this->hash($values['password']));
        $record->set('activation_hash', $hash);
        $record->save();
        return $hash;
    }

    public function sendRegisterEmail($email, $hash) {
        $this->mailer->addAddress($email);
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
        $hash = md5(time());
        $record->set('forgot_hash', $hash);
        $record->save();
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

}