<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
 */
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\Extension\Twig\AsseticExtension;

require(dirname(__DIR__).'/vendor/autoload.php');
require(dirname(__DIR__).'/vendor/ulogin/config/all.inc.php');
require(dirname(__DIR__).'/vendor/ulogin/main.inc.php');

define('PUBLIC_DIR', __DIR__);
define('SITE_DIR', dirname(__DIR__).'/site');
define('TEMPLATE_DIR', SITE_DIR.'/templates');
define('CACHE_DIR', SITE_DIR.'/cache');

require(SITE_DIR.'/config/db.php');


/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$site = new \Jack\Site();
$app = $site->app;
$view = $app->view();

$site->addService('db', function() use ($db_config) {
	$db = new PDO("mysql:host=$db_config[host];dbname=$db_config[name]", $db_config['user'], $db_config['pass']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
});
$site->addService('assets', function() {
	$assets = new AssetFactory(SITE_DIR.'/assets');
	$am = new AssetManager();
	$assets->setAssetManager($am);
	$assets->addWorker(new CacheBustingWorker());	
	//$view->getInstance()->addExtension(new AsseticExtension($assets, true));
	return $assets;
});
$site->addService('asset writer', function() {
	return new AssetWriter(PUBLIC_DIR.'/assets');
});

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

// GET route
$app->get('/', array($site, 'requireLogin'), function () use ($site, $app) {
	$app->render('parts/home.twig');
})->setName('home');

/**************************************************************
************************ Users ********************************
/*************************************************************/
$app->get('/user/login', function () use ($app, $view) {
	$app->render('parts/user/login-form.twig', array(
		'nonce' => \ulNonce::Create('login'),
		'destination' => (isset($_GET['destination']) ? $_GET['destination'] : '/'),
		'email' => isset($_GET['email']) ? $_GET['email'] : '',
		'title' => $view->get('title') . '| Login',
	));
})->setName('login');
$app->post('/user/login', array($site, 'actionLogin'));

/**************************************************************
************************ Admin ********************************
/*************************************************************/
$app->get('/admin', array($site, 'requireAdmin'), function () use ($app, $view) {
	$app->render('admin/parts/home.twig');
})->setName('admin/home');
$app->get('/admin/invites', array($site, 'requireAdmin'), function () use ($app, $view) {
	$app->render('admin/parts/invites.twig');
})->setName('admin/invites');
$app->post('/admin/invites', array($site, 'requireAdmin'), array($site, 'actionInvite'));

/**************************************************************
************************ Issues *******************************
/*************************************************************/
$app->get('/issues', array($site, 'requireLogin'), function () use ($site, $app) {
	$app->render('parts/issues.twig', array(
		'issues' => $site->getIssues(),
	));
})->setName('issues');
$app->get('/issues/:slug', array($site, 'requireLogin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$app->render('parts/issue.twig', array(
		'title' => $view->get('title').' | '.$issue['title'],
		'issue' => $issue,
		'covers' => array(
			'front' => $site->asset("issues/$slug/covers/front.jpg"),
			'back' => $site->asset("issues/$slug/covers/back.jpg"),
			'index' => $site->asset("issues/$slug/covers/index.jpg"),
		),
		'cover_poster' => array(
			'left' => $site->asset("issues/$slug/cover-poster/left.jpg"),
			'middle' => $site->asset("issues/$slug/cover-poster/middle.jpg"),
			'right' => $site->asset("issues/$slug/cover-poster/right.jpg"),
		),
		'page' => array(
			'front_left' => $site->asset("issues/$slug/pages/1/left.jpg"),
			'front_right' => $site->asset("issues/$slug/pages/1/right.jpg"),
			'back_left' => $site->asset("issues/$slug/pages/2/left.jpg"),
			'back_right' => $site->asset("issues/$slug/pages/2/right.jpg"),
		),
		'centerfold' => array(
			'front_top_left' => $site->asset("issues/$slug/centerfold/front/top-left.jpg"),
			'front_top_right' => $site->asset("issues/$slug/centerfold/front/top-right.jpg"),
			'front_bottom_left' => $site->asset("issues/$slug/centerfold/front/bottom-left.jpg"),
			'front_bottom_right' => $site->asset("issues/$slug/centerfold/front/bottom-right.jpg"),
			'back_top_left' => $site->asset("issues/$slug/centerfold/back/top-right.jpg"),
			'back_top_right' => $site->asset("issues/$slug/centerfold/back/top-left.jpg"),
			'back_bottom_left' => $site->asset("issues/$slug/centerfold/back/bottom-right.jpg"),
			'back_bottom_right' => $site->asset("issues/$slug/centerfold/back/bottom-left.jpg"),
		),
		'thumbs' => array(
			'covers' => $site->asset("issues/$slug/covers/thumb.jpg"),
			'cover_poster' => $site->asset("issues/$slug/cover-poster/thumb.jpg"),
			'page' => array(
				'front' => $site->asset("issues/$slug/pages/1/thumb.jpg"),
				'back' => $site->asset("issues/$slug/pages/2/thumb.jpg"),
			),
			'centerfold' => array(
				'front' => $site->asset("issues/$slug/centerfold/front/thumb.jpg"),
				'back' => $site->asset("issues/$slug/centerfold/back/thumb.jpg"),
			),
		),
	));
})->setName('issue');


// POST route
$app->post(
    '/post',
    function () {
        echo 'This is a POST route';
    }
);

// PUT route
$app->put(
    '/put',
    function () {
        echo 'This is a PUT route';
    }
);

// PATCH route
$app->patch('/patch', function () {
    echo 'This is a PATCH route';
});

// DELETE route
$app->delete(
    '/delete',
    function () {
        echo 'This is a DELETE route';
    }
);

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
