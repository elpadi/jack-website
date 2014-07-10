<?php

require(ROOT_DIR.'/src/functions.php');

define('PATH_PREFIX', count(array_intersect(array_filter(explode('/', PUBLIC_DIR)), array_filter(explode('/', $_SERVER['REQUEST_URI'])))) > 0 ? get_longest_common_subsequence(PUBLIC_DIR.'/', $_SERVER['REQUEST_URI']) : '/');
define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);

require(SITE_DIR.'/config/site.php');
require(SITE_DIR.'/config/db.php');
require(SITE_DIR.'/config/smtp.php');
require(SITE_DIR.'/config/invite.php');

defined('DEBUG') or define('DEBUG', IS_LOCAL);
ini_set('display_errors', DEBUG ? 'on' : 'off');
error_reporting(DEBUG ? E_ERROR | E_WARNING | E_PARSE | E_NOTICE : 0);

require(VENDOR_DIR.'/autoload.php');
require(ROOT_DIR.'/src/vendor/owasp/phprbac/PhpRbac/autoload.php');
$site = new Jack\Site();
$app = $site->app;
$view = $app->view();
$it = new RecursiveDirectoryIterator(ROOT_DIR.'/src/routes');
foreach(new RecursiveIteratorIterator($it) as $file) {
	if (is_file($file)) {
		require($file);
	}
}
require(ROOT_DIR.'/src/services.php');
$site->init();
