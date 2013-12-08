<?php
namespace Jack;

class Site implements AssetManager {

	public $app;

	protected $uLogin;
	protected $userIsLoggedIn = false;

	protected $services;
	
	protected $tablePrefix = 'jack_';
	protected $lastStatement;

	function __construct() {
		$this->services = array(
			'constructors' => array(),
			'instances' => array(),
		);
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
		));
		$this->app = $app;

		if (!\sses_running()) {
			\sses_start();
		}
		$this->userIsLoggedIn = isset($_SESSION['uid']) && isset($_SESSION['username']) && isset($_SESSION['loggedIn']) && ($_SESSION['loggedIn']===true);
	}

	public function requireLogin(\Slim\Route $route) {
		if (false && !$this->userIsLoggedIn) {
			$this->app->flash('error', 'Login required.');
			$this->app->redirect('/user/login?destination=' . $_SERVER['REQUEST_URI']);
		}
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

	public function actionRegister() {
		$uLogin = new \uLogin();
		$uLogin->CreateUser($_POST['email'], $_POST['password']);
		$this->actionLogin();
	}

	public function actionInvite() {
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
		return preg_replace('/\{(.*?)}/', '`'.$this->tablePrefix.'$1`', $sql);
	}

	protected function query($sql, $params=array()) {
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

	public function getIssues() {
		$issues = array();
		$stmt = $this->query('SELECT * FROM {issues}');
		while ($issue = $stmt->fetch(\PDO::FETCH_CLASS, 'Jack\Issue')) {
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
		//$stmt = $this->query('SELECT `filename` FROM {pages} WHERE `issue_id`=? ORDER BY `sort_order` ASC', array($issue['id'])); 
		return $issue;
	}

	public function basePath() {
		return SITE_DIR.'/assets';
	}

	public function asset($path) {
		$this->getService('logger')->addDebug("Getting asset with path: $path.");
		$assets = $this->getService('assets');
		$asset = $assets->createAsset(array($path));
		$this->getService('logger')->addDebug("Got asset. Last modified: ".$asset->getLastModified());
		$path = '/assets/'.$asset->getTargetPath();
		if (!is_file(PUBLIC_DIR.$path)) {
			$aw = $this->getService('asset writer');
			$aw->writeAsset($asset);
		}
		return $path;
	}

}

interface AssetManager {
	public function asset($path);
	public function basePath();
}

class Issue {

	public $id;
	public $slug;
	public $title;
	public $pages;

	public $covers = array();

	protected $imagine;

	const PAGE_WIDTH = 450;
	const PAGE_HEIGHT = 600;

	public function hydrate(AssetManager $assets) {
		if (empty($this->covers)) {
			$this->covers = array(
				'front' => $assets->asset("issues/$this->slug/covers/front.jpg"),
			);
		}
	}

	public function update($data, $files, AssetManager $assets) {
		$this->imagine = new \Imagine\Gd\Imagine();
		foreach ($files as $key => $info) {
			if (strpos($info['type'], 'image/jpeg') !== 0) {
				throw new \InvalidArgumentException("The file sent is not a valid JPEG image.");
			}
			call_user_func(array($this, "update$key"), $info['tmp_name'], $assets);
		}
		$this->generateThumbs($assets);
	}
	
	public function generateThumbs(AssetManager $assets) {
	}

	public function updateFrontCover($imagePath, AssetManager $assets) {
		$path = "issues/$this->slug/covers/front.jpg";
		$dims = $this->imagine->open($imagePath)->getSize();
		if ($dims->getWidth() !== self::PAGE_WIDTH) {
			throw new \InvalidArgumentException("The width of the image is ".$dims->getWidth().", not the required ".self::PAGE_WIDTH.".");
		}
		if ($dims->getHeight() !== self::PAGE_HEIGHT) {
			throw new \InvalidArgumentException("The height of the image is ".$dims->getHeight().", not the required ".self::PAGE_HEIGHT.".");
		}
		if (!move_uploaded_file($imagePath, $assets->basePath().'/'.$path)) {
			throw new \RuntimeException("Error saving the image.");
		}
		sleep(2); // give the script some time before moving the file, to create a difference between access times.
		clearstatcache();
		$this->covers['front'] = $assets->asset($path);
	}

	public static function log($msg) {
		global $site;
		$site->getService('logger')->addDebug($msg);
	}

	/*
	public function getCoverPath() {
		return $this->path("covers/front");
	}

	protected function path($partial) {
		return "issues/$this->slug/$partial.jpg";
	}

	public static function createFromSlug($slug) {
		global $site;
		$stmt = $this->query('SELECT * FROM {issues} WHERE `slug`=?', array($slug));
		$issue = $this->lastStatement->fetch(\PDO::FETCH_ASSOC);
		$stmt = $this->query('SELECT `filename` FROM {pages} WHERE `issue_id`=? ORDER BY `sort_order` ASC', array($issue['id'])); 
	}
	 */

}
