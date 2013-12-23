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

}
