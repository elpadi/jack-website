<?php
namespace Website;

use Functional as F;
use Jack\Images;

class App extends \Jack\App {

	public function __construct() {
		parent::__construct();
		$this->_framework = new \Slim\App(new \Slim\Container(['settings' => [
			'displayErrorDetails' => DEBUG,
			'determineRouteBeforeAppMiddleware' => true,
		]]));
	}

	public function run() {
		$this->_router->loadRoutes(WEBSITE_DIR.'/src/routes');
		$this->_router->enableRoutes();
		static::framework()->run();
	}

	public static function prefix($s) {
		return "jack_website__$s";
	}

	public function createAction($request, $response, $args=[]) {
		return new Action($request, $response, $args);
	}	
	
	public static function createTemplate() {
		return new Template();
	}	

	public static function createAssetManager() {
		return new AssetManager();
	}	

	public function imageUrl($path, $size) {
		return Images::resizeImage($path, $size);
	}

	public static function setIntroAsSeen() {
		if (!static::hasSeenIntro()) setcookie(static::prefix('has_seen_intro'), '1', time() + 3600 * 24000, PUBLIC_ROOT);
	}

	public static function hasSeenIntro() {
		return isset($_COOKIE[static::prefix('has_seen_intro')]) && $_COOKIE[static::prefix('has_seen_intro')] === '1';
	}

	public static function contactEmail() {
		return static::email($_POST['email'], \Jack\Jack::$config->get('contact_email'), 'Message sent through the contact form', $_POST['message']);
	}

}
