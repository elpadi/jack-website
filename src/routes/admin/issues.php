<?php
use Website\App;
use Website\Issue;

$routes[] = array(
	'path' => '/admin/issues',
	'routes' => array(
		array(
			'name' => 'list-issues',
			'method' => 'get',
			'path' => '',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('admin/issues/list', ['issues' => Issue::all()]));
			},
		),
		array(
			'name' => 'add-issue',
			'method' => 'get',
			'path' => '/add',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('admin/issues/add'));
			},
		),
		array(
			'method' => 'post',
			'path' => '/add',
			'action' => function($request, $response, $args) {
				Issue::handleSubmission($_POST);
				return $response->withRedirect(App::routeLookup('list-issues'));
			},
		),
		array(
			'name' => 'edit-issue',
			'method' => 'get',
			'path' => '/{slug}',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('admin/issues/edit', ['issue' => Issue::bySlug($args['slug'])]));
			},
		),
	),
	'middleware' => array(
		function ($request, $response, $next) {
			return App::userCan('edit issues') ? $next($request, $response) : App::notAuthorized($response);
		}
	),
);
