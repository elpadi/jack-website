<?php
define('WEBSITE_DIR', dirname(__DIR__));

// if looking for a file, and not a php file, it must be a 404
call_user_func(function() {
	$last = basename($_SERVER['REQUEST_URI']);
	if (strpos($last, '.') !== FALSE && !strpos($last, '.php')) {
		header("HTTP/1.0 404 Not Found");
		die($_SERVER['REQUEST_URI'] . ' was not found.');
	}
});

require(WEBSITE_DIR.'/vendor/autoload.php');

// load configuration
$env = new Dotenv\Dotenv(WEBSITE_DIR);
$env->load();

// load cockpit
require(PUBLIC_ROOT_DIR.'/admin/bootstrap.php');

