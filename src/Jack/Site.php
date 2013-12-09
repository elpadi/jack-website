<?php
namespace Jack;

use Imagine\Image\Box;
use Imagine\Image\Point;

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
	public $centerfold = array();

	protected $imagine;

	const PAGE_WIDTH = 450;
	const PAGE_HEIGHT = 600;
	
	const THUMB_HEIGHT = 100;

	public function hydrate(AssetManager $assets) {
		if (empty($this->covers)) {
			$this->covers = array(
				'front' => $assets->asset("issues/$this->slug/covers/front.jpg"),
				'back' => $assets->asset("issues/$this->slug/covers/back.jpg"),
				'index' => $assets->asset("issues/$this->slug/covers/index.jpg"),
				'poster' => $assets->asset("issues/$this->slug/cover-poster/original.jpg"),
			);
		}
		if (empty($this->centerfold)) {
			$this->centerfold = array(
				'front' => $assets->asset("issues/$this->slug/centerfold/front/original.jpg"),
				'back' => $assets->asset("issues/$this->slug/centerfold/back/original.jpg"),
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
		$this->updateCoverImage('front', $imagePath, $assets);
	}

	public function updateBackCover($imagePath, AssetManager $assets) {
		$this->updateCoverImage('back', $imagePath, $assets);
	}

	public function updateIndex($imagePath, AssetManager $assets) {
		$this->updateCoverImage('index', $imagePath, $assets);
	}

	public function updateCoverPoster($imagePath, AssetManager $assets) {
		$imageBox = new Box(self::PAGE_WIDTH * 3, self::PAGE_HEIGHT);
		$image = $this->updateImage("cover-poster/original", $imageBox, $imagePath, $assets);
		$pageBox = new Box(self::PAGE_WIDTH, self::PAGE_HEIGHT);
		$base = $assets->basePath()."/issues/$this->slug/cover-poster";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		$image->copy()->crop(new Point(0, 0), $pageBox)->save($path("left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, 0), $pageBox)->save($path("middle"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH * 2, 0), $pageBox)->flipHorizontally()->save($path("right"));
		$image->resize($imageBox->heighten(self::THUMB_HEIGHT))->save($path("thumb"));
		$this->covers["poster"] = $assets->asset($path("original"));
	}

	public function updateFrontCenterfold($imagePath, AssetManager $assets) {
		$imageBox = new Box(self::PAGE_WIDTH * 2, self::PAGE_HEIGHT * 2);
		$image = $this->updateImage("centerfold/front/original", $imageBox, $imagePath, $assets);
		$pageBox = new Box(self::PAGE_WIDTH, self::PAGE_HEIGHT);
		$base = $assets->basePath()."/issues/$this->slug/centerfold/front";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		$image->copy()->crop(new Point(0, 0), $pageBox)->save($path("top-left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, 0), $pageBox)->save($path("top-right"));
		$image->copy()->crop(new Point(0, self::PAGE_HEIGHT), $pageBox)->save($path("bottom-left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, self::PAGE_HEIGHT), $pageBox)->save($path("bottom-right"));
		$image->resize($imageBox->heighten(self::THUMB_HEIGHT * 2))->save($path("thumb"));
		$this->covers["poster"] = $assets->asset($path("original"));
	}

	protected function updateImage($partialPath, Box $imageBox, $imagePath, AssetManager $assets) {
		$path = "issues/$this->slug/$partialPath.jpg";
		$image = $this->imagine->open($imagePath);
		$dims = $image->getSize();
		if ($dims->getWidth() !== $imageBox->getWidth()) {
			throw new \InvalidArgumentException("The width of the image is ".$dims->getWidth()." px, not the required ".$imageBox->getWidth()." px.");
		}
		if ($dims->getHeight() !== $imageBox->getHeight()) {
			throw new \InvalidArgumentException("The height of the image is ".$dims->getHeight()." px, not the required ".$imageBox->getHeight()." px.");
		}
		if (!move_uploaded_file($imagePath, $assets->basePath().'/'.$path)) {
			throw new \RuntimeException("Error saving the image.");
		}
		clearstatcache();
		return $image;
	}

	protected function updateCoverImage($section, $imagePath, AssetManager $assets) {
		if (!isset($this->covers[$section])) {
			throw new \InvalidArgumentException("Bad cover section '$section'.");
		}
		$this->updateImage("covers/$section", new Box(self::PAGE_WIDTH, self::PAGE_HEIGHT), $imagePath, $assets);
		$base = $assets->basePath()."/issues/$this->slug/covers";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		
		$thumbBox = new Box(self::PAGE_WIDTH * 3, self::PAGE_HEIGHT);
		$thumb = $this->imagine->create($thumbBox);
		$thumb->paste($this->imagine->open($path("back")), new Point(0, 0));
		$thumb->paste($this->imagine->open($path("front")), new Point(self::PAGE_WIDTH, 0));
		$thumb->paste($this->imagine->open($path("index")), new Point(self::PAGE_WIDTH * 2, 0));
		$thumb->resize($thumbBox->heighten(self::THUMB_HEIGHT))->save($path("thumb"));
	
		$this->covers[$section] = $assets->asset($path($section));
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
