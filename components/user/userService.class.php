<?php

class UserService {

    private $config;
    private $table;
    private $user;
    private $mailer;
    private $translation;

    public function __construct($im) {
        $this->config = $im->get('config');
        $this->table = $im->get('userTable');
        $this->user = $im->get('user');
        $this->mailer = $im->get('mailer');
        $this->translation = $im->get('translation');
    }

    public function hash($value) {
        return md5($this->config->get('user.salt').$value);
    }

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

    public function findById($id) {
        return $this->table->findOne(null, [
            'where' => [
                ['id', '=', $id]
            ]
        ]);
    }

    public function findByEmail($email) {
        return $this->table->findOne(null, [
            'where' => [
                ['email', '=', $email]
            ]
        ]);
    }

    public function findByActivationHash($hash) {
        return $this->table->findOne(null, [
            'where' => [
                ['activation_hash', '=', $hash]
            ]
        ]);
    }

    public function findByForgotHash($hash) {
        return $this->table->findOne(null, [
            'where' => [
                ['forgot_hash', '=', $hash]
            ]
        ]);
    }

    public function login($email, $password) {
        $record = $this->findActiveByEmailAndPassword($email, $password);
        if (!$record) {
            return false;
        }
        $record->set('last_login', time());
        $record->save();
        foreach ($record->getAttributes() as $name => $value) {
            $this->user->set($name, $value);
        }
        $this->user->setLoggedIn(true);
        return true;
    }

    public function logout() {
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
        return $this->sendRegisterEmail($values['email'], $hash);
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

    public function changeForgotPassword($record, $password) {
        $record->set('forgot_hash', '');
        $record->set('password', $this->hash($password));
        $record->save();
    }

}