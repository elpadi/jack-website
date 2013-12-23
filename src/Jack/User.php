<?php
namespace Jack;

class User {

	public $id = 0;
	public $username = "";
	public $password = "";
	public $plainpass = "";

	public function __construct() {
	}

	public function setData($data) {
		$this->id = isset($data['id']) ? (int)($data['id']) : 0;
		if (isset($data['username'])) {
			$this->username = $data['username'];
		}
		if (isset($data['plainpass'])) {
			$this->plainpass = $data['plainpass'];
		}
	}

	public function save(DbAccess $db) {
		$uLogin = new \uLogin();
		if ($this->id === 0) {
			$uLogin->CreateUser($this->username, $this->plainpass);
		}
	}

	public function login() {
		$uLogin = new \uLogin(function($uid, $username, $uLogin) {
			$_SESSION['uid'] = $uid;
			$_SESSION['username'] = $username;
			$_SESSION['loggedIn'] = true;
			$uLogin->SetAutologin($username, true);
		}, function($uid, $username, $uLogin) {
			throw new \Exception("Error logging in.");
		});
		$uLogin->Authenticate($this->username, $this->plainpass);
	}

}
