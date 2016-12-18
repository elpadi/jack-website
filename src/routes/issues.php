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
			'args' => ['slug' => 'dare-to-dream'],
		),
		array(
			'name' => 'issue',
			'path' => '/{slug}',
		),
		array(
			'name' => 'editorial',
			'path' => '/{slug}/{part:[0-9]+}',
		),
		array(
			'name' => 'section',
			'path' => '/{slug}/{section:[-a-z0-9]+}',
		),
		/*
		array(
			'name' => 'layouts',
			'path' => '/{slug}/layouts',
		),
		array(
			'name' => 'layout',
			'path' => '/{slug}/layouts/{layout}',
		),
		 */
	),
);
