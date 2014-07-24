<?php
namespace Jack;

class User extends \ptejada\uFlex\User {

	public $userData;

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
		$this->RegDate = time();
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

	public function fetchData($users_db) {
		if (!$this->userData) {
			$stmt = $users_db->query('SELECT * FROM `user_data` WHERE `user_id`='.intval($this->ID));
			$this->userData = $stmt->fetch(\PDO::FETCH_ASSOC);
		}
	}

}
