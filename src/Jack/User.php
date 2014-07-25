<?php
namespace Jack;

class User extends \ptejada\uFlex\User {

	public $userData;

	public function __construct(array $info=array()) {
		global $users_db_config;
		parent::__construct($info);
		$this->config->database->dsn = "mysql:host=$users_db_config[host];dbname=$users_db_config[name]";
		$this->config->database->host = $users_db_config['host'];
		$this->config->database->name = $users_db_config['name'];
		$this->config->database->user = $users_db_config['user'];
		$this->config->database->password = $users_db_config['pass'];
		$this->start(false);
	}

	public function addNew($db, $acl, $info, $data) {
		if (parent::register($info, false)) {
			// add data row.
			$stmt = $db->prepare("INSERT INTO `user_data` SET `user_id`=?, `full_name`=?, `company`=?, `position`=?");
			$stmt->execute(array($this->_data['ID'], $data['fullname'], $data['company'], $data['position']));
			// add roles.
			if (isset($data['roles'])) {
				foreach (explode('&', $data['roles']) as $roleId) {
					$acl->Users->assign($roleId, $this->_data['ID']);
				}
			}
			return true;
		}
		return false;
	}

	public function fetchData($users_db) {
		if (!$this->userData) {
			$stmt = $users_db->query('SELECT * FROM `user_data` WHERE `user_id`='.intval($this->ID));
			$this->userData = $stmt->fetch(\PDO::FETCH_ASSOC);
		}
	}

	public function clearData() {
		$this->_data = array();
	}

}
