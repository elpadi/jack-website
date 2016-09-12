<?php
use Website\App;
use Website\AssetManager;
use Website\Model;
use Website\Issue;

$routes[] = array(
	'path' => '/issues',
	'routes' => array(
		array(
			'name' => 'issues',
			'path' => '',
			'action' => 'issue',
			'args' => ['slug' => '1-dare-to-dream'],
		),
		array(
			'name' => 'issue',
			'path' => '/{slug}',
		),
		array(
			'name' => 'editorial',
			'path' => '/{slug}/editorial-{part}',
		),
		array(
			'name' => 'layout',
			'path' => '/{issue}/editorial-{part}/{layout}',
			'action' => function($request, $response, $args) {
				$issue = Model::bySlug('issue', $args['issue']);
				return $response->write(App::render('issues/layout', ['issue' => $issue, 'layout' => Issue::getLayout($issue->getNumber(), $args['part'], $args['layout'])]));
			},
		),
	),
	/*
	'middleware' => array(
		function ($request, $response, $next) {
			$args = $request->getAttribute('routeInfo')[2];
			try {
				if (isset($args['part']) && !in_array($args['part'], ['1','2'])) throw new InvalidArgumentException("Issue part not found.");
				if (isset($args['slug'])) Model::bySlug('issue', $args['slug']);
			}
			catch (Exception $e) {
				return App::notFound($response, $e);
			}
			return $next($request, $response);
		}
	),
	 */
);
