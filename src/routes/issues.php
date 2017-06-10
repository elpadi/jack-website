<?php
$routes[] = array(
	'name' => 'issues',
	'path' => '/issues',
	'routes' => array(
		array(
			'name' => 'issues',
			'path' => '',
			'action' => 'editorial',
			'args' => ['slug' => 'dare-to-dream'],
		),
		array(
			'name' => 'issue',
			'path' => '/{slug}',
			'action' => 'editorial',
			'args' => ['slug' => 'dare-to-dream'],
		),
		array(
			'name' => 'editorial',
			'path' => '/{slug}/editorial',
		),
		array(
			'name' => 'layouts',
			'path' => '/{slug}/layouts[/part-{part:[0-9]+}]',
		),
		array(
			'name' => 'layout',
			'path' => '/{slug}/layouts/{layout}',
		),
		array(
			'name' => 'section',
			'path' => '/{slug}/editorial/{section:[-a-z0-9]+}',
		),
	),
);
