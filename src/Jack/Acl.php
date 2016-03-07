<?php
namespace Jack;

class Acl {

	public function __construct() {
		global $users_db_config;
		$this->rbac = new \PhpRbac\Rbac(array(
			'host' => $users_db_config['host'],
			'user' => $users_db_config['user'],
			'pass' => $users_db_config['pass'],
			'dbname' => $users_db_config['name'],
			'tablePrefix' => 'rbac_',
			'adapter' => 'pdo_mysql',
		));
	}

	public function userCan($permission, User $user) {
		return $this->rbac->check($permission, $user->isSigned() ? $user->ID : GUEST_USER_ID);
	}

}
