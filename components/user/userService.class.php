<?php

class UserService {

	private $config;
	private $table;
	private $user;
    private $mailer;

	public function __construct($im) {
		$this->config = $im->get('config');
		$this->table = $im->get('userTable');
		$this->user = $im->get('user');
        $this->mailer = $im->get('mailer');
	}

	public function hash($value) {
		return md5($this->config->get('user.salt').$value);
	}

	private function findByEmailAndPassword($email, $password) {
		$record = $this->table->findOne(null, [
			'where' => [
				['email', '=', $email],
				['password', '=', $this->hash($password)]
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
        $record = $this->findByEmailAndPassword($email, $password);
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
	    $record = new Record($this->table);
        $record->setAll($fields, $values);
        $record->set('password', $this->hash($values['password']));
        $record->set('activation_hash', md5(time()));
        $record->save();
        return $this->mailer->send($values['email']);
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
        $record->set('forgot_hash', md5(time()));
        $record->save();
        return $this->mailer->send($email);
    }

    public function changeForgotPassword($record, $password) {
        $record->set('forgot_hash', '');
        $record->set('password', $this->hash($password));
        $record->save();
    }

}