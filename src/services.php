<?php
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\Extension\Twig\AsseticExtension;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$site->addService('db', function() use ($db_config) {
	$db = new PDO("mysql:host=$db_config[host];dbname=$db_config[name]", $db_config['user'], $db_config['pass']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
});
$site->addService('users_db', function() use ($users_db_config) {
	$users_db = new PDO("mysql:host=$users_db_config[host];dbname=$users_db_config[name]", $users_db_config['user'], $users_db_config['pass']);
	$users_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $users_db;
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
		'debug' => IS_LOCAL,
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
$site->addService('acl', function() {
	global $users_db_config;
	$rbac = new PhpRbac\Rbac(array(
		'host' => $users_db_config['host'],
		'user' => $users_db_config['user'],
		'pass' => $users_db_config['pass'],
		'dbname' => $users_db_config['name'],
		'tablePrefix' => 'rbac_',
		'adapter' => 'pdo_mysql',
	));
	return $rbac;
});

$site->addService('user', function() {
	$user = new Jack\User();
	$user->start(true);
	return $user;
});

$site->addService('nonce', function() {
	return new Wukka\Nonce(NONCE_SECRET, 91);
});

