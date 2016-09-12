<?php
namespace Website;

use \Functional as F;

class App extends \Jack\App {

	public function __construct() {
		parent::__construct();
		$this->_framework = new \Slim\App(new \Slim\Container(['settings' => [
			'displayErrorDetails' => DEBUG,
			'determineRouteBeforeAppMiddleware' => true,
		]]));
		$this->loadRoutes(WEBSITE_DIR.'/src/routes');
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

	public function imageUrl($object) {
		/*
		switch (end(explode('\\', get_class($object)))) {
			case 'Poster':
				return static::$assets->url(sprintf('issue-%d/posters/%s-%s_%dx%d.jpg',
					$object->getLayout()->getIssue()->getNumber(),
					$object->getPage(),
					strtolower($object->getFace()),
					$object->getRow(),
					$object->getCol()
				));
			case 'Layout': 
				$pages = array_filter(F\invoke($object->getPosters(), 'getPage'));
				return static::$assets->url(sprintf('issue-%d/layouts/%s.jpg',
					$object->getIssue()->getNumber(),
					(count($pages) ? implode('-', $pages).'_' : '').str_replace('-','_',$object->getSlug())
				));
		}
		 */
		return '';
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
