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

