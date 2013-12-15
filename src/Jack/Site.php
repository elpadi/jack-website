<?php
namespace Jack;

use Imagine\Image\Box;
use Imagine\Image\Point;

class Site implements AssetManager,DbAccess {

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
			'isLocal' => in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")),
			'pathPrefix' => PATH_PREFIX,
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
		$stmt = $this->query('SELECT * FROM {posters} JOIN {issue_posters} ON poster_id=id WHERE `issue_id`=? ORDER BY `sort_order` ASC', array($issueId));
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'Jack\Poster');
		while ($poster = $stmt->fetch(\PDO::FETCH_CLASS)) {
			$poster->hydrate($this);
			$posters[] = $poster;
		}
		return $posters;
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

function slug($s, $separator="-") {
	return trim(preg_replace('/[^a-zA-Z0-9]+/', $separator, strtolower($s)), $separator);
}

class Issue {

	public $id;
	public $slug;
	public $title;
	public $posters;

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
				'thumb' => $assets->asset("issues/$this->slug/covers/thumb.jpg"),
				'poster' => $assets->asset("issues/$this->slug/cover-poster/original.jpg"),
				'poster_left' => $assets->asset("issues/$this->slug/cover-poster/left.jpg"),
				'poster_middle' => $assets->asset("issues/$this->slug/cover-poster/middle.jpg"),
				'poster_right' => $assets->asset("issues/$this->slug/cover-poster/right.jpg"),
				'poster_thumb' => $assets->asset("issues/$this->slug/cover-poster/thumb.jpg"),
			);
		}
		if (empty($this->centerfold)) {
			$this->centerfold = array(
				'front' => $assets->asset("issues/$this->slug/centerfold/front/original.jpg"),
				'front_top_left' => $assets->asset("issues/$this->slug/centerfold/front/top-left.jpg"),
				'front_top_right' => $assets->asset("issues/$this->slug/centerfold/front/top-right.jpg"),
				'front_bottom_left' => $assets->asset("issues/$this->slug/centerfold/front/bottom-left.jpg"),
				'front_bottom_right' => $assets->asset("issues/$this->slug/centerfold/front/bottom-right.jpg"),
				'front_thumb' => $assets->asset("issues/$this->slug/centerfold/front/thumb.jpg"),
				'back' => $assets->asset("issues/$this->slug/centerfold/back/original.jpg"),
				'back_top_left' => $assets->asset("issues/$this->slug/centerfold/back/top-left.jpg"),
				'back_top_right' => $assets->asset("issues/$this->slug/centerfold/back/top-right.jpg"),
				'back_bottom_left' => $assets->asset("issues/$this->slug/centerfold/back/bottom-left.jpg"),
				'back_bottom_right' => $assets->asset("issues/$this->slug/centerfold/back/bottom-right.jpg"),
				'back_thumb' => $assets->asset("issues/$this->slug/centerfold/back/thumb.jpg"),
			);
		}
	}

	public function updatePosterOrder($data, DbAccess $db) {
		$sth = $db->prepare("UPDATE `".$db->table("issue_posters")."` SET `sort_order`=? WHERE `issue_id`=$this->id AND `poster_id`=?");
		$order = 1;
		for ($i = 0; $i < count($data) / 2; $i++) {
			$sth->execute(array($order, $data['row'.$i.'_front']));
			$order++;
			$sth->execute(array($order, $data['row'.$i.'_back']));
			$order++;
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
		$image->copy()->crop(new Point(0, 0), $pageBox)->flipHorizontally()->save($path("left"));
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
		$this->centerfold["front"] = $assets->asset($path("original"));
	}

	public function updateBackCenterfold($imagePath, AssetManager $assets) {
		$imageBox = new Box(self::PAGE_WIDTH * 2, self::PAGE_HEIGHT * 2);
		$image = $this->updateImage("centerfold/back/original", $imageBox, $imagePath, $assets);
		$pageBox = new Box(self::PAGE_WIDTH, self::PAGE_HEIGHT);
		$base = $assets->basePath()."/issues/$this->slug/centerfold/back";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		$image->copy()->crop(new Point(0, 0), $pageBox)->flipHorizontally()->save($path("top-left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, 0), $pageBox)->flipHorizontally()->save($path("top-right"));
		$image->copy()->crop(new Point(0, self::PAGE_HEIGHT), $pageBox)->flipHorizontally()->save($path("bottom-left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, self::PAGE_HEIGHT), $pageBox)->flipHorizontally()->save($path("bottom-right"));
		$image->resize($imageBox->heighten(self::THUMB_HEIGHT * 2))->save($path("thumb"));
		$this->centerfold["back"] = $assets->asset($path("original"));
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

}

class Poster {
	
	public $id;
	public $slug;
	public $title;
	public $description;
	
	public $poster;
	public $thumb;

	public $left;
	public $right;
	public $left_flipped;
	public $right_flipped;

	public $issueId;

	public function hydrate(AssetManager $assets) {
		if (empty($this->poster)) {
			$this->poster = $assets->asset("posters/$this->id/original.jpg");
			$this->thumb = $assets->asset("posters/$this->id/thumb.jpg");
			$this->left = $assets->asset("posters/$this->id/left.jpg");
			$this->left_flipped = $assets->asset("posters/$this->id/left-flipped.jpg");
			$this->right = $assets->asset("posters/$this->id/right.jpg");
			$this->right_flipped = $assets->asset("posters/$this->id/right-flipped.jpg");
		}
	}

	protected function updateData($data, DbAccess $db) {
		if (!isset($data['id']) || !ctype_digit($data['id'])) {
			throw new \Exception("Invalid data.");
		}
		if (isset($data['title'])) {
			$data['slug'] = slug($data['title']);
		}
		$middleSql = "SET `title`=?, `slug`=?, `description`=? ";
		if ($data['id'] !== '0') {
			$sql = "UPDATE `".$db->table("posters")."` ".$middleSql."WHERE `id`=".intval($this->id);
			$db->query($sql, array($data['title'], $data['slug'], $data['description']));
		}
		else {
			$sql = "INSERT INTO `".$db->table("posters")."` ".$middleSql;
			$db->query($sql, array($data['title'], $data['slug'], $data['description']));
			$this->id = $db->lastInsertId();
			$sql = "INSERT INTO `".$db->table("issue_posters")."` SET `issue_id`=?, `poster_id`=?";
			$db->query($sql, array($this->issueId, $this->id));
		}
		foreach (array('title','slug','description') as $key) {
			$this->$key = $data[$key];
		}
	}

	protected function updatePoster($info, AssetManager $assets) {
		if (strpos($info['type'], 'image/jpeg') !== 0) {
			throw new \InvalidArgumentException("The file sent is not a valid JPEG image.");
		}
		$base = $assets->basePath()."/posters/$this->id";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		$imageBox = new Box(Issue::PAGE_WIDTH * 2, Issue::PAGE_HEIGHT);
		$pageBox = new Box(Issue::PAGE_WIDTH, Issue::PAGE_HEIGHT);
		$image = $this->imagine->open($info['tmp_name']);
		$dims = $image->getSize();
		if ($dims->getWidth() !== $imageBox->getWidth()) {
			throw new \InvalidArgumentException("The width of the image is ".$dims->getWidth()." px, not the required ".$imageBox->getWidth()." px.");
		}
		if ($dims->getHeight() !== $imageBox->getHeight()) {
			throw new \InvalidArgumentException("The height of the image is ".$dims->getHeight()." px, not the required ".$imageBox->getHeight()." px.");
		}
		if (!move_uploaded_file($info['tmp_name'], $path("original"))) {
			throw new \RuntimeException("Error saving the image.");
		}
		clearstatcache();
		$image->copy()->crop(new Point(0, 0), $pageBox)->save($path("left"));
		$image->copy()->crop(new Point(0, 0), $pageBox)->flipHorizontally()->save($path("left-flipped"));
		$image->copy()->crop(new Point(Issue::PAGE_WIDTH, 0), $pageBox)->save($path("right"));
		$image->copy()->crop(new Point(Issue::PAGE_WIDTH, 0), $pageBox)->flipHorizontally()->save($path("right-flipped"));
		$image->resize($imageBox->heighten(Issue::THUMB_HEIGHT))->save($path("thumb"));
		$this->poster = $assets->asset($path("original"));
	}

	public function update($data, $files, AssetManager $assets, DbAccess $db) {
		if (!empty($data)) {
			$this->updateData($data, $db);
			if ($data['id'] === '0') {
				$path = $assets->basePath()."/posters/$this->id";
				!is_dir($path) && mkdir($path);
			}
		}
		if (!empty($files) && isset($files['poster'])) {
			$this->imagine = new \Imagine\Gd\Imagine();
			$this->updatePoster($files['poster'], $assets);
		}
	}

}
