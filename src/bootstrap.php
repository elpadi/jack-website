<?php

define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);

require(VENDOR_DIR.'/autoload.php');
require(ROOT_DIR.'/src/vendor/owasp/phprbac/PhpRbac/autoload.php');

foreach (array_filter(glob(SITE_DIR.'/config/*.php'), function($path) { return !strpos($path, 'remote') && !strpos($path, 'sample'); }) as $path) require($path);

defined('DEBUG') or define('DEBUG', IS_LOCAL);
ini_set('display_errors', DEBUG ? 'on' : 'off');
error_reporting(DEBUG ? E_ERROR | E_WARNING | E_PARSE | E_NOTICE : 0);

\Jack\App::$framework = new \Slim\App;

foreach (array_merge(glob(ROOT_DIR.'/src/routes/*.php'), glob(ROOT_DIR.'/src/routes/admin/*.php')) as $routes) require($routes);

\Jack\App::$framework->run();
exit(0);
