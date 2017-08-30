<?php
define('PUBLIC_ROOT_DIR', dirname(dirname(__DIR__)));
define('ROOT_DIR', dirname(PUBLIC_ROOT_DIR));

$jack_dir = isset($_ENV['JACK_DIR']) ? $_ENV['JACK_DIR'] : dirname(ROOT_DIR).'/common';
$app_dir = isset($_ENV['APP_DIR']) ? $_ENV['APP_DIR'] : ROOT_DIR;
define('PUBLIC_ROOT', '/' . (isset($_ENV['APP_PUBLIC_DIR']) ? $_ENV['APP_PUBLIC_DIR'].'/' : ''));

require(realpath($jack_dir).'/src/bootstrap.php');
require(realpath($app_dir).'/src/bootstrap.php');

$user = cockpit('cockpit')->getUser();
if (!$user || $user['group'] !== 'admin') {
	header('HTTP/1.0 403 Forbidden');
	echo 'You do not have access to this script. Login first if you have an admin account.';
	exit(0);
}

function out($s) {
	echo "<p>$s</p>";
}

@ob_end_flush();
