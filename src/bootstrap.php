<?php
use Website\App;
use Symfony\Component\Finder\Finder;

define('WEBSITE_DIR', dirname(__DIR__));

App::$framework = new \Slim\App(new \Slim\Container(['settings' => ['displayErrorDetails' => DEBUG]]));

$routes = array();
$finder = new Symfony\Component\Finder\Finder();
$finder->files()->in(__DIR__.'/routes');
foreach ($finder as $file) require($file->getRealpath());

foreach ($routes as $group) {
	$app_group = App::$framework->group($group['path'], function() use ($group) {
		foreach ($group['routes'] as $route) {
			$app_route = call_user_func(array(App::$framework, $route['method']), $route['path'], $route['action']);
			if (isset($route['name'])) $app_route->setName($route['name']);
		}
	});
}
