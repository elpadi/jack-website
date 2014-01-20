<?php
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\Extension\Twig\AsseticExtension;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function delTree($dir) {
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

function get_longest_common_subsequence($string_1, $string_2)
{
	$string_1_length = strlen($string_1);
	$string_2_length = strlen($string_2);
	$return          = "";
 
	if ($string_1_length === 0 || $string_2_length === 0)
	{
		// No similarities
		return $return;
	}
 
	$longest_common_subsequence = array();
 
	// Initialize the CSL array to assume there are no similarities
	for ($i = 0; $i < $string_1_length; $i++)
	{
		$longest_common_subsequence[$i] = array();
		for ($j = 0; $j < $string_2_length; $j++)
		{
			$longest_common_subsequence[$i][$j] = 0;
		}
	}
 
	$largest_size = 0;
 
	for ($i = 0; $i < $string_1_length; $i++)
	{
		for ($j = 0; $j < $string_2_length; $j++)
		{
			// Check every combination of characters
			if ($string_1[$i] === $string_2[$j])
			{
				// These are the same in both strings
				if ($i === 0 || $j === 0)
				{
					// It's the first character, so it's clearly only 1 character long
					$longest_common_subsequence[$i][$j] = 1;
				}
				else
				{
					// It's one character longer than the string from the previous character
					$longest_common_subsequence[$i][$j] = $longest_common_subsequence[$i - 1][$j - 1] + 1;
				}
 
				if ($longest_common_subsequence[$i][$j] > $largest_size)
				{
					// Remember this as the largest
					$largest_size = $longest_common_subsequence[$i][$j];
					// Wipe any previous results
					$return       = "";
					// And then fall through to remember this new value
				}
 
				if ($longest_common_subsequence[$i][$j] === $largest_size)
				{
					// Remember the largest string(s)
					$return = substr($string_1, $i - $largest_size + 1, $largest_size);
				}
			}
			// Else, $CSL should be set to 0, which it was already initialized to
		}
	}
 
	// Return the list of matches
	return $return;
}

define('PUBLIC_DIR', __DIR__);

define('PATH_PREFIX', count(array_intersect(array_filter(explode('/', PUBLIC_DIR)), array_filter(explode('/', $_SERVER['REQUEST_URI'])))) > 0 ? get_longest_common_subsequence(PUBLIC_DIR.'/', $_SERVER['REQUEST_URI']) : '/');
define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")));
define('DEBUG', IS_LOCAL);

if (!DEBUG) {
	ini_set('display_errors','off');
}

require(dirname(__DIR__).'/config.dirs.php');

require(SITE_DIR.'/config/site.php');
require(SITE_DIR.'/config/db.php');
require(SITE_DIR.'/config/smtp.php');
require(SITE_DIR.'/config/invite.php');

require(VENDOR_DIR.'/autoload.php');
require(VENDOR_DIR.'/ulogin/config/all.inc.php');
require(VENDOR_DIR.'/ulogin/main.inc.php');

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$site = new Jack\Site();
$app = $site->app;
$view = $app->view();

$site->addService('db', function() use ($db_config) {
	$db = new PDO("mysql:host=$db_config[host];dbname=$db_config[name]", $db_config['user'], $db_config['pass']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
});
$site->addService('smtp', function() use ($smtp_config) {
	require_once(dirname(__DIR__).'/vendor/swiftmailer/swiftmailer/lib/swift_init.php');
	if (DEBUG) {
		$transport = Swift_SendmailTransport::newInstance();
	}
	else {
		$transport = Swift_SmtpTransport::newInstance($smtp_config['host'], $smtp_config['port']);
		$transport->setUsername($smtp_config['user'])
			->setPassword($smtp_config['pass']);
	}
	$mailer = Swift_Mailer::newInstance($transport);
	return $mailer;
});
$site->addService('templates', function() use ($smtp_config) {
	$loader = new Twig_Loader_Filesystem(TEMPLATE_DIR);
	$twig = new Twig_Environment($loader, array(
		'cache' => CACHE_DIR.'/twig',
	));
	return $twig;
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
$site->addService('logger', function() {
	$log = new Logger('name');
	$log->pushHandler(new StreamHandler(SITE_DIR.'/logs/debug.log', Logger::DEBUG));
	return $log;
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
$app->get('/', array($site, 'requireLogin'), function () use ($site, $app, $view) {
	$app->render('parts/home.twig', array(
		'title' => $view->get('title') . ' | Poster size magazine',
		'section' => 'home',
		'first_issue' => $site->getFirstIssue(),
	));
})->setName('home');

/**************************************************************
************************ Admin ********************************
/*************************************************************/
$app->get('/admin', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/home.twig', array(
		'sections' => $site->getAdminSections('Dashboard'),
	));
})->setName('admin/home');

$app->get('/admin/users', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/users.twig', array(
		'sections' => $site->getAdminSections('Users'),
	));
})->setName('admin/users');
$app->get('/admin/users/create', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/user-add.twig', array(
		'sections' => $site->getAdminSections('Users'),
	));
})->setName('admin/create-user');
$app->post('/admin/users/create', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$user = new Jack\User();
	try {
		$user->setData($app->request->post());
		$user->save($site);
	}
	catch (\Exception $e) {
		echo "User not saved.";
		if (DEBUG) {
			echo ' --- '.$e->getFile().':'.$e->getLine().' - '.$e->getMessage();
			exit(1);
		}
	}
	$app->flash('info', "User '$user->username' saved.");
	$app->redirect($app->urlFor('admin/users'));
});


$app->get('/admin/invites', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/invites.twig', array(
		'title' => $view->get('title').' | Invites',
		'sections' => $site->getAdminSections('Invites'),
		'invites' => $site->getInvites(),
	));
})->setName('admin/invites');
$app->get('/admin/invites/send', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$id = $app->request->get('id');
	$invite = $id ? $site->getInviteById($id) : new Jack\Invite();
	$app->render('admin/parts/invites/send.twig', array(
		'title' => $view->get('title').' | Send Invite',
		'sections' => $site->getAdminSections('Invites'),
		'invite' => $invite,
	));
})->setName('admin/send-invite');
$app->post('/admin/invites/send', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$invite = new Jack\Invite();
	try {
		$invite->setData($app->request->post());
		$invite->save($site);
		$invite->hydrate($site, $site);
		$invite->send($site, $site, $site);
	}
	catch (\Exception $e) {
		echo "Invite not sent.";
		if (DEBUG) {
			echo ' --- '.$e->getFile().':'.$e->getLine().' - '.$e->getMessage();
			exit(1);
		}
	}
	$app->flash('info', "Invite sent to $invite->email.");
	$app->redirect($app->urlFor('admin/invites'));
});

$app->get('/admin/issues', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/issues.twig', array(
		'title' => $view->get('title').' | Issues',
		'issues' => $site->getIssues(),
	));
})->setName('admin/issues');
$app->get('/admin/issues/:slug', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	try {
		$issue = $site->getIssueBySlug($slug);
	}
	catch (\Exception $e) {
		if ($e->getCode() === Jack\Site::E_NOT_FOUND) {
			return $app->notFound();
		}
		echo $e->getFile().':'.$e->getLine().'  '.$e->getMessage();
		exit(0);
	}
	$app->render('admin/parts/issue.twig', array(
		'title' => $view->get('title').' | Edit '.$issue->title,
		'issue' => $issue,
		'sections' => $site->getAdminSections('Issues'),
	));
})->setName('admin/issue');
$app->post('/admin/issues/:slug', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	try {
		$issue->update($app->request->post(), $site, $site);
		$app->flash('info', "The issue '$issue->title' was successfully updated.");
	}
	catch (\Exception $e) {
		$app->flash('error', "Error updating the issue. ".$e->getFile().':'.$e->getLine().'  '.$e->getMessage());
	}
	$app->redirect($app->urlFor('admin/issue', array('slug' => $issue->slug)));
})->setName('admin/issue/update');
$app->post('/admin/issues/:slug/images', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$app->response->headers->set('Content-Type', 'application/json');
	$issue = $site->getIssueBySlug($slug);
	try {
		$issue->updateImages($_FILES, $site);
		echo json_encode(array('success' => true, 'issue' => $issue));
	}
	catch (\Exception $e) {
		echo json_encode(array('success' => false, 'error' => $e->getFile().':'.$e->getLine().'  '.$e->getMessage()));
	}
})->setName('admin/issue/update-images');

$app->get('/admin/issues/:slug/pages', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('admin/parts/pages.twig', array(
		'title' => $view->get('title').' | Edit order '.$issue->title,
		'sections' => $site->getAdminSections('Issues'),
		'issue' => $issue,
		'posters' => $posters,
	));
})->setName('admin/issue/pages');
$app->post('/admin/issues/:slug/pages', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$app->response->headers->set('Content-Type', 'application/json');
	$issue = $site->getIssueBySlug($slug);
	try {
		$issue->updatePosterOrder($app->request->post(), $site);
		echo json_encode(array('success' => true));
	}
	catch (\Exception $e) {
		echo json_encode(array('success' => false, 'error' => $e->getFile().':'.$e->getLine().'  '.$e->getMessage()));
	}
});
$app->get('/admin/issues/:slug/pages/add', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$app->render('admin/parts/add_page.twig', array(
		'title' => $view->get('title').' | Add poster to '.$issue->title,
		'issue' => $site->getIssueBySlug($slug),
		'sections' => $site->getAdminSections('Issues'),
	));
})->setName('admin/issue/newpage');
$app->post('/admin/issues/:slug/pages/add', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$poster = new Jack\Poster();
	$poster->issueId = $issue->id;
	try {
		$poster->update($app->request->post(), $_FILES, $site, $site);
		$app->flash('info', "The poster '$poster->title' was added to this issue.");
		$app->redirect($app->urlFor('admin/issue', array('slug' => $slug)));
	}
	catch (\Exception $e) {
		echo "Adding poster failed.";
		if (DEBUG) {
			echo ' --- '.$e->getFile().':'.$e->getLine().' - '.$e->getMessage();
		}
	}
});
$app->post('/admin/issues/poster/delete/:id', array($site, 'requireAdmin'), function ($id) use ($site, $app, $view) {
	$app->response->headers->set('Content-Type', 'application/json');
	$poster = $site->getPosterById($id);
	try {
		$poster->delete($site, $site);
		echo json_encode(array('success' => true));
	}
	catch (\Exception $e) {
		echo json_encode(array('success' => false, 'error' => $e->getFile().':'.$e->getLine().'  '.$e->getMessage()));
	}
})->setName('admin/delete-poster');

/**************************************************************
************************ Issues *******************************
/*************************************************************/
$app->get('/issues', array($site, 'requireLogin'), function () use ($site, $app, $view) {
	$app->render('parts/issues.twig', array(
		'title' => $view->get('title').' | Issues',
		'issues' => $site->getIssues(),
		'section' => 'issues',
	));
})->setName('issues');
$app->get('/issues/:slug', array($site, 'requireLogin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('parts/issue.twig', array(
		'title' => $view->get('title').' | '.$issue->title,
		'issue' => $issue,
		'posters' => $posters,
		'section' => 'issue',
	));
})->setName('issue');


/**************************************************************
************************ Users ********************************
/*************************************************************/
$app->get('/user/login', function () use ($app, $view) {
	$app->render('parts/user/login-form.twig', array(
		'nonce' => \ulNonce::Create('login'),
		'destination' => (isset($_GET['destination']) ? $_GET['destination'] : '/'),
		'email' => isset($_GET['email']) ? $_GET['email'] : '',
		'title' => $view->get('title') . ' | Login',
		'section' => 'login',
	));
})->setName('login');
$app->post('/user/login', array($site, 'actionLogin'));


/**************************************************************
************************ Invites ******************************
/*************************************************************/
$app->get('/invite/confirmation', function () use ($site, $app, $view) {
	$app->render('parts/invites/confirmation.twig', array(
		'section' => 'invite-confirmation',
	));
})->setName('invite/confirmation');
$app->get('/invite/:hash', function ($hash) use ($invite_config, $site, $app, $view) {
	if ($site->isUserLoggedIn()) { 
		$app->redirect($app->urlFor('home'));
	}
	$invite = $site->getInviteByHash($hash);
	if (!$invite) {
		$app->notFound();
	}
	try {
		$invite->hydrate($site, $site);
		$invite->recordUse($site);
		$user = new Jack\User();
		$user->setData($invite_config['user']);
		$user->login();
	}
	catch (\Exception $e) {
		echo "Could not log in.";
		if (DEBUG) {
			echo ' --- '.$e->getFile().':'.$e->getLine().' - '.$e->getMessage();
		}
		exit(1);
	}
	$app->redirect($app->urlFor(empty($invite->uses) ? 'invite/confirmation' : 'home'));
})->setName('invite');



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
ob_start();
$app->run();
ob_end_flush();
