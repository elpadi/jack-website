<?php
$routes[] = array(
	'name' => 'issues',
	'path' => '/issues',
	'routes' => array(
		array(
			'name' => 'issues',
			'path' => '',
		),
	),
);
$routes[] = array(
	'name' => 'issue',
	'path' => '/issues/{id:[0-9]+}-{slug}',
	'routes' => array(
		array(
			'name' => 'issue',
			'path' => '',
		),
		array('name' => 'editorial'),
		array(
			'name' => 'section',
			'path' => '/editorial/{section:[-a-z0-9]+}',
		),
		array('name' => 'layouts'),
		array(
			'name' => 'layout',
			'path' => '/layouts/{layout}',
		),
	),
);
