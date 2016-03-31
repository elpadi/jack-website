<?php
use Website\App;
use Symfony\Component\Finder\Finder;

define('WEBSITE_DIR', dirname(__DIR__));

require(WEBSITE_DIR.'/vendor/autoload.php');
App::$framework = new \Slim\App(new \Slim\Container(['settings' => [
	'displayErrorDetails' => DEBUG,
	'determineRouteBeforeAppMiddleware' => true,
]]));

$routes = array();
$finder = new Symfony\Component\Finder\Finder();
$finder->files()->in(__DIR__.'/routes');
foreach ($finder as $file) require($file->getRealpath());

foreach ($routes as $group) {
	$app_group = App::$framework->group($group['path'], function() use ($group) {
		foreach ($group['routes'] as $route) {
			if (!isset($route['method'])) $route['method'] = 'get';
			if (!isset($route['path'])) $route['path'] = $route['name'];
			if (!isset($route['vars'])) $route['vars'] = array();
			if (!isset($route['action'])) $route['action'] = function($request, $response, $args) use ($route) {
				return $response->write(App::render($route['name'], $route['vars']));
			};
			$app_route = call_user_func(array(App::$framework, $route['method']), $route['path'], $route['action']);
			if (isset($route['name'])) $app_route->setName($route['name']);
		}
	});
	foreach ($group['middleware'] as $fn) $app_group->add($fn);
}
