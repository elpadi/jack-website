<?php
use Website\App;
use Symfony\Component\Finder\Finder;

define('WEBSITE_DIR', dirname(__DIR__));

require(WEBSITE_DIR.'/vendor/autoload.php');
App::$framework = new \Slim\App(new \Slim\Container(['settings' => [
	'displayErrorDetails' => DEBUG,
	'determineRouteBeforeAppMiddleware' => true,
]]));

App::loadRoutes(__DIR__.'/routes', 'Website\App');
