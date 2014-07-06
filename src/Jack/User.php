<?php
namespace Jack;

class User extends \ptejada\uFlex\User {

	public function __construct(array $info=array()) {
		global $users_db_config;
		$this->config['database'] = array(
			'dsn' => "mysql:host=$users_db_config[host];dbname=$users_db_config[name]",
			'user' => $users_db_config['user'],
			'password' => $users_db_config['pass'],
			'host' => $users_db_config['host'],
			'name' => $users_db_config['name'],
		);
		parent::__construct($info);
	}

	public function addNew($db, $acl, $info, $data) {
		if (parent::register($info, false)) {
			// add data row.
			$stmt = $db->prepare("INSERT INTO `user_data` SET `user_id`=?, `full_name`=?, `company`=?, `position`=?");
			$stmt->execute(array($this->_data['ID'], $data['fullname'], $data['company'], $data['position']));
			// add roles.
			foreach (explode('&', $data['roles']) as $roleId) {
				$acl->Users->assign($roleId, $this->_data['ID']);
			}
		}
		else {
			throw new \Exception("Error registering new user.");
		}
	}
	/*
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
	 */

}
