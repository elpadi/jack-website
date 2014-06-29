<?php

require(ROOT_DIR.'/src/functions.php');

define('PATH_PREFIX', count(array_intersect(array_filter(explode('/', PUBLIC_DIR)), array_filter(explode('/', $_SERVER['REQUEST_URI'])))) > 0 ? get_longest_common_subsequence(PUBLIC_DIR.'/', $_SERVER['REQUEST_URI']) : '/');
define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")));
define('DEBUG', IS_LOCAL);

if (!DEBUG) {
	ini_set('display_errors','off');
}

require(SITE_DIR.'/config/site.php');
require(SITE_DIR.'/config/db.php');
require(SITE_DIR.'/config/smtp.php');
require(SITE_DIR.'/config/invite.php');

require(VENDOR_DIR.'/autoload.php');

$site = new Jack\Site();
$app = $site->app;
$view = $app->view();

require(ROOT_DIR.'/src/services.php');

$it = new RecursiveDirectoryIterator(ROOT_DIR.'/src/routes');
foreach(new RecursiveIteratorIterator($it) as $file) {
	if (is_file($file)) {
		require($file);
	}
}
