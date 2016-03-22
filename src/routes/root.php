<?php
use Website\App;

$routes[] = array(
	'path' => '/',
	'routes' => array(
		array(
			'name' => 'home',
			'method' => 'get',
			'path' => '',
			'action' => function($request, $response, $args) {
				return $response->withRedirect(App::routeLookup('intro'));
				return $response->withRedirect(App::routeLookup('issue', ['slug' => '1-dare-to-dream']));
			},
		),
		array('name' => 'intro'),
		array('name' => 'about'),
		array('name' => 'team'),
		array('name' => 'art-gallery'),
	),
	'middleware' => array(
	),
);
