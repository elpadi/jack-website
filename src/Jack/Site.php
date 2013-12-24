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
	public function parseTemplate($template, $vars);
}

interface Router {
	public function url($route, $args);
}

class Site implements AssetManager,DbAccess,EmailSender,TemplateHandler,Router {

	public $app;

	protected $uLogin;
	protected $userIsLoggedIn = false;

	protected $services;
	
	protected $lastStatement;

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
			'title' => 'JACK',
			'isLocal' => in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")),
			'pathPrefix' => PATH_PREFIX,
			'userAgent' => $userAgent->toArray(),
		));
		$this->app = $app;

		if (!\sses_running()) {
			\sses_start();
		}
		$this->userIsLoggedIn = isset($_SESSION['uid']) && isset($_SESSION['username']) && isset($_SESSION['loggedIn']) && ($_SESSION['loggedIn']===true);
	}

	public function isUserLoggedIn() {
		return $this->userIsLoggedIn;
	}

	public function requireLogin(\Slim\Route $route) {
		if (false && !$this->userIsLoggedIn) {
			$this->app->flash('error', 'Login required.');
			$this->app->redirect('/user/login?destination=' . $_SERVER['REQUEST_URI']);
		}
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

	public function requireAdmin(\Slim\Route $route) {
		return true;
		if (false && !$this->userIsLoggedIn || $_SESSION['uid'] !== 1) {
			$this->app->flash('error', 'Not authorized.');
			$this->app->redirect('/');
		}
	}

	public function actionLogin() {
		$app = $this->app;
		$success = function() use ($app) {
			$app->redirect($_POST['destination']);
		};
		$error = function($msg) use ($app) {
			$app->flash('error', $msg);
			$app->redirect('/user/login?destination='.$_POST['destination'].'&email='.$_POST['email']);
		};
		new \uLogin();
		$uLogin = new \uLogin(function($uid, $username, $uLogin) use ($success) {
			$_SESSION['uid'] = $uid;
			$_SESSION['username'] = $username;
			$_SESSION['loggedIn'] = true;
			$uLogin->SetAutologin($username, true);
			$success();
		}, function($uid, $username, $uLogin) use ($error) {
			$error('Invalid email/password combination.');
		});
		if (!isset($_POST['nonce']) || !\ulNonce::Verify('login', $_POST['nonce'])) {
			$error('Invalid submission. Please try again.');
		}
		$uLogin->Authenticate($_POST['email'], $_POST['password']);
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
		if (count($params)) {
			$this->lastStatement = $this->getService('db')->prepare($sql);
			$this->lastStatement->execute($params);
		}
		else {
			$this->lastStatement = $this->getService('db')->query($sql);
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
		$smtp->send($message);
	}

	public function parseTemplate($template, $vars) {
		return $this->getService('templates')->render("$template.twig", $vars);
	}

	public function url($route, $args) {
		return $this->app->urlFor($route, $args);
	}

	public function getInviteById($id) {
		$stmt = $this->query('SELECT * FROM {invites} WHERE `id`=?', array($id));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Invite');
		$invite = $stmt->fetch(\PDO::FETCH_CLASS);
		return $invite;
	}

	public function getInviteByHash($hash) {
		$stmt = $this->query('SELECT * FROM {invites} WHERE `hash`=?', array($hash));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Invite');
		$invite = $stmt->fetch(\PDO::FETCH_CLASS);
		return $invite;
	}

	public function getInvites() {
		$invites = array();
		$stmt = $this->query('SELECT * FROM {invites}');
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Invite');
		while ($invite = $stmt->fetch(\PDO::FETCH_CLASS)) {
			$invite->hydrate($this, $this);
			$invites[] = $invite;
		}
		return $invites;
	}

	public function getIssues() {
		$issues = array();
		$stmt = $this->query('SELECT * FROM {issues}');
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Issue');
		while ($issue = $stmt->fetch(\PDO::FETCH_CLASS)) {
			$issue->hydrate($this);
			$issues[] = $issue;
		}
		return $issues;
	}

	public function getIssueBySlug($slug) {
		$stmt = $this->query('SELECT * FROM {issues} WHERE `slug`=?', array($slug));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Issue');
		$issue = $stmt->fetch(\PDO::FETCH_CLASS);
		$issue->hydrate($this);
		return $issue;
	}

	public function getPostersByIssueId($issueId) {
		$posters = array();
		$i = 0;
		$stmt = $this->query('SELECT * FROM {posters} JOIN {issue_posters} ON poster_id=id WHERE `issue_id`=? ORDER BY `sort_order` ASC', array($issueId));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Poster');
		while ($poster = $stmt->fetch(\PDO::FETCH_CLASS)) {
			$poster->hydrate($this);
			$posters[floor($i / 2)][$i % 2 === 0 ? 'front' : 'back'] = $poster;
			$i++;
		}
		return $posters;
	}

	public function getPosterById($id) {
		$stmt = $this->query('SELECT * FROM {posters} WHERE `id`=?', array($id));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Poster');
		$poster = $stmt->fetch(\PDO::FETCH_CLASS);
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

