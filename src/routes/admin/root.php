<?php
use Website\App;

$routes[] = array(
	'path' => '/admin',
	'routes' => array(
		array(
			'name' => 'admin',
			'method' => 'get',
			'path' => '',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('admin/index'));
			},
		),
	),
	'middleware' => array(
		function ($request, $response, $next) {
			return App::userCan('access admin') ? $next($request, $response) : App::notAuthorized($response);
		}
	),
);
