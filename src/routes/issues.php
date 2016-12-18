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
			'name' => 'section',
			'path' => '/{slug}/editorial-{part}/{section}',
		),
		array(
			'name' => 'layouts',
			'path' => '/{slug}/layouts',
		),
		array(
			'name' => 'layout',
			'path' => '/{slug}/layouts/{layout}',
		),
	),
);
