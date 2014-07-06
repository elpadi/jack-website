<?php
namespace Jack;

use Functional as F;

interface AssetManager {
	public function asset($path);
	public function basePath();
}

interface DbAccess {
	const TABLE_PREFIX = 'jack_';
	public function query($sql, $params=array());
	public function prepare($sql);
	public function table($name);
	public function lastInsertId();
}

interface EmailSender {
	public function sendHtml($email, $subject, $from, $bodies);
}

interface TemplateHandler {
	public function parseTemplate($template, $vars=array());
}

interface Router {
	public function url($route, $args);
	public function redirect($path);
}

class Site implements AssetManager,DbAccess,EmailSender,TemplateHandler,Router {

	public $app;

	protected $uLogin;

	protected $services;
	
	protected $lastStatement;

	private static $adminIds = array(1,3);

	const E_NOT_FOUND = 1;

	function __construct() {
		$this->services = array(
			'constructors' => array(),
			'instances' => array(),
		);
		$userAgent = new \phpUserAgent();
		$app = new \Slim\Slim(array(
			'view' => new \Slim\Views\Twig()
		));
		$view = $app->view();
		$view->twigTemplateDirs = array(TEMPLATE_DIR);
		$view->parserOptions = array(
			'debug' => true,
			'cache' => CACHE_DIR.'/twig',
			'twigTemplateDirs' => array(TEMPLATE_DIR),
		);
		$view->parserExtensions = array(
			new \Slim\Views\TwigExtension(),
		);
		$view->appendData(array(
			'CONTACT_EMAIL' => 'dah@thejackmag.com',
			'IS_LOCAL' => IS_LOCAL,
			'CAN_ACCESS_ADMIN' => $this->hasPermission('access admin'),
			'IS_LOGGED_IN' => intval($this->getService('user')->ID) > 0,
			'BASE_TITLE' => 'JACK | ',
			'pathPrefix' => PATH_PREFIX,
			'userAgent' => $userAgent->toArray(),
		));
		$this->app = $app;
	}

	public function getAdminSections($currentSection) {
		$app = $this->app;
		return F\map(array(
			'admin/home' => 'Dashboard',
			'admin/users' => 'Users',
			'admin/invites' => 'Invites',
			'admin/issues' => 'Issues',
		), function($title, $route) use ($currentSection, $app) {
			return array('title' => $title, 'url' => $app->urlFor($route), 'selected' => $title === $currentSection);
		});
	}

	protected function hasPermission($permission) {
		$user = $this->getService('user');
		if (!$user->isSigned()) {
			return false;
		}
		return $this->getService('acl')->check($permission, $user->ID);
	}

	public function checkPermission($permission) {
		if (!$this->hasPermission($permission)) {
			$this->redirect('/forbidden');
		}
	}

	public function addService($name, $constructor) {
		$this->services['constructors'][$name] = $constructor;
	}

	public function getService($name) {
		if (!isset($this->services['instances'][$name])) {
			$this->services['instances'][$name] = call_user_func($this->services['constructors'][$name]);
		}
		return $this->services['instances'][$name];
	}

	protected function preprocessSql($sql) {
		return preg_replace('/\{(.*?)}/', '`'.self::TABLE_PREFIX.'$1`', $sql);
	}

	public function table($name) {
		return self::TABLE_PREFIX.$name;
	}

	public function query($sql, $params=array()) {
		if ($this->lastStatement) {
			$this->lastStatement->closeCursor();
		}
		$sql = $this->preprocessSql($sql);
		try {
			if (count($params)) {
				$this->lastStatement = $this->getService('db')->prepare($sql);
				$this->lastStatement->execute($params);
			}
			else {
				$this->lastStatement = $this->getService('db')->query($sql);
			}
		}
		catch (PDOException $e) {
			if (DEBUG) {
				var_dump(__FILE__.":".__LINE__." - ".__METHOD__, $e->getMessage(), $e->getTrace());
			}
			exit(0);
		}
		return $this->lastStatement;
	}
	
	public function prepare($sql) {
		$this->lastStatement = $this->getService('db')->prepare($sql);
		return $this->lastStatement;
	}

	public function lastInsertId() {
		return $this->getService('db')->lastInsertId();
	}

	public function sendHtml($email, $subject, $from, $bodies) {
		$smtp = $this->getService('smtp');
		$message = \Swift_Message::newInstance();
		$message->setSubject($subject)
			->setFrom($from)
			->setTo($email)
			->setCc($from)
			->setBody($bodies['plain'])
			->addPart($bodies['html'], 'text/html');
		if (!$smtp->send($message, $failures)) {
			throw new \Exception("No emails were sent. Rejected addresses: " . implode(', ', $failures));
		}
	}

	public function parseTemplate($template, $vars=array()) {
		return $this->getService('templates')->render("$template.twig", $vars);
	}

	public function url($route, $args) {
		return $this->app->urlFor($route, $args);
	}

	public function redirect($path) {
		if ($path[0] === '/') {
			$path = substr($path, 1);
		}
		return $this->app->redirect(PATH_PREFIX.$path);
	}

	public function getInviteById($id) {
		$stmt = $this->query('SELECT * FROM {invites} WHERE `id`=?', array($id));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Invite');
		$invite = $stmt->fetch();
		return $invite;
	}

	public function getInviteByHash($hash) {
		$stmt = $this->query('SELECT * FROM {invites} WHERE `hash`=?', array($hash));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Invite');
		$invite = $stmt->fetch();
		return $invite;
	}

	public function fetchUsersData() {
		$users = array();
		$stmt = $this->getService('users_db')->query('SELECT * FROM `Users` JOIN `user_data` ON `Users`.`ID`=`user_id`');
		while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			$row['roles'] = array_map(function($role) { return $role['Title']; }, $this->getService('acl')->Users->allRoles($row['ID']));
			$users[] = $row;
		}
		return $users;
	}

	public function getInvites() {
		$invites = array();
		$stmt = $this->query('SELECT * FROM {invites}');
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Invite');
		do {
			$invite = $stmt->fetch();
			if (!$invite) {
				if (count($invites) < $stmt->rowCount()) {
					throw new \Exception("Error processing row.");
				}
				break;
			}
			$invites[] = $invite;
		} while (1);
		foreach ($invites as &$invite) {
			$invite->hydrate($this, $this);
		}
		return $invites;
	}

	public function getIssues() {
		$issues = array();
		$stmt = $this->query('SELECT * FROM {issues}');
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Issue');
		while ($issue = $stmt->fetch()) {
			$issue->hydrate($this);
			$issues[] = $issue;
		}
		return $issues;
	}

	public function getIssue($sql, $vars=array()) {
		$stmt = $this->query("SELECT {issues}.*, CONCAT(LOWER(`season`),'-',`year`) AS slug, CONCAT(`season`,' ',`year`) AS title FROM {issues} $sql", $vars);
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Issue');
		$issue = $stmt->fetch();
		if (!$issue) {
			throw new \Exception("Issue with sql '$sql' not found.", self::E_NOT_FOUND);
		}
		$issue->hydrate($this);
		return $issue;
	}

	public function getFirstIssue() {
		return $this->getIssue("ORDER BY `year`,`season` ASC");
	}

	public function getIssueBySlug($slug) {
		$parts = explode('-', $slug);
		if (count($parts) !== 2) {
			throw new \InvalidArgumentException("Invalid slug '$slug'.");
		}
		try {
			$issue = $this->getIssue('WHERE `season`=? AND `year`=?', $parts);
		}
		catch (\Exception $e) {
			if ($e->getCode() === self::E_NOT_FOUND) {
				throw new \Exception("Issue with slug '$slug' not found.", self::E_NOT_FOUND);
			}
			throw $e;
		}
		return $issue;
	}

	public function getPostersByIssueId($issueId) {
		$posters = array();
		$i = 0;
		$stmt = $this->query('SELECT * FROM {posters} JOIN {issue_posters} ON poster_id=id WHERE `issue_id`=? ORDER BY `sort_order` ASC', array($issueId));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Poster');
		while ($poster = $stmt->fetch()) {
			$poster->hydrate($this);
			$posters[floor($i / 2)][$i % 2 === 0 ? 'front' : 'back'] = $poster;
			$i++;
		}
		return $posters;
	}

	public function getPosterById($id) {
		$stmt = $this->query('SELECT * FROM {posters} WHERE `id`=?', array($id));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Poster');
		$poster = $stmt->fetch();
		$poster->hydrate($this);
		return $poster;
	}

	public function basePath() {
		return SITE_DIR.'/assets';
	}

	public function asset($path) {
		$this->getService('logger')->addDebug("Getting asset with path: $path.");
		$assets = $this->getService('assets');
		$asset = $assets->createAsset(array($path));
		$this->getService('logger')->addDebug("Got asset. Last modified: ".$asset->getLastModified());
		$path = PATH_PREFIX.'assets/'.$asset->getTargetPath();
		if (!is_file(PUBLIC_DIR.$path)) {
			$aw = $this->getService('asset writer');
			$aw->writeAsset($asset);
		}
		return $path;
	}

}

