<?php
use Website\App;
use Website\Model;

$routes[] = array(
	'path' => '/issues',
	'routes' => array(
		array(
			'name' => 'issues',
			'method' => 'get',
			'path' => '',
			'action' => function($request, $response, $args) {
				return $response->withRedirect(App::routeLookup('issue', ['slug' => '1-dare-to-dream']));
			},
		),
		array(
			'name' => 'issue',
			'method' => 'get',
			'path' => '/{slug}',
			'action' => function($request, $response, $args) {
				try {
					return $response->write(App::render('issues/single', ['issue' => Model::bySlug('issue', $args['slug'])]));
				}
				catch (Exception $e) {
					return App::notFound($response, $e);
				}
			},
		),
	),
	'middleware' => array(
	),
);
