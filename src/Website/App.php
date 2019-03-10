<?php
namespace Website;

use Functional as F;
use Jack\Logging\Logger;
use Website\Shop\Cart;
use Website\Shop\Catalog;
use Website\Shop\Square\Checkout;

class App extends \Jack\App {

	public function __construct() {
		parent::__construct();
		$this->initSlim();
		$this->initLogger();
	}

	protected function initLogger() {
		Logger::$logsDir = WEBSITE_DIR.'/logs';
	}

	protected function initSlim() {
		$c = new \Slim\Container(['settings' => [
			'displayErrorDetails' => DEBUG,
			'determineRouteBeforeAppMiddleware' => true,
		]]);
		$c['phpErrorHandler'] = function ($c) {
			return function ($request, $response, $error) use ($c) {
				global $app;
				return $app->errorResponse($response, $error);
			};
		};
		$c['errorHandler'] = function ($c) {
			return function ($request, $response, $exception) use ($c) {
				global $app;
				return $app->errorResponse($response, $exception);
			};
		};
		$c['notFoundHandler'] = function ($c) {
			return function ($request, $response) use ($c) {
				global $app;
				return $app->errorResponse($response, new \Exception('Page not found.', 404));
			};
		};
		$this->_framework = new \Slim\App($c);
	}

	public function run() {
		$this->_router->loadRoutes(WEBSITE_DIR.'/src/routes');
		$this->_router->enableRoutes();
		static::$container['events']->addListener('action.edit_possibilities', function($e) {
			$possibilities = $e->getArgument('possibilities');
			if ($e->getArgument('route')['name'] === 'issue') {
				$class = str_replace(' ', '', ucwords(str_replace('-', ' ', $e->getArgument('routeArgs')['slug'])));
				array_unshift($possibilities, ['class','Issue',$class]);
				$e->setArgument('possibilities', $possibilities);
			}
		});
		static::framework()->run();
	}

	public static function prefix($s) {
		return "jack_website__$s";
	}

	public static function createTemplate() {
		return new Template();
	}	

	public static function createAssetManager() {
		return new AssetManager();
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
