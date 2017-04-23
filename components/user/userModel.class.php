<?php

class UserModel {

	private $config;
	private $table;
	private $user;

	public function __construct($config, $db, $user) {
		$this->config = $config;
		$this->table = new UserTable($db);
		$this->user = $user;
	}

	private function hash($value) {
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

	public function login($email, $password) {
		$record = $this->findByEmailAndPassword($email, $password);
		if ($record) {
			$record->set('last_login', time());
			$record->save();		
			foreach ($record->getAttributes() as $name => $value) {
				$this->user->set($name, $value);
			}
			$this->user->setLoggedIn(true);
			return true;
		}
		return false;
	}

	public function logout() {
		$this->user->destroy();
	}

	public function findById($id) {
		return $this->table->findOne(null, [
			'where' => [
				['id', '=', $id]
			]
		]);
	}
	
}