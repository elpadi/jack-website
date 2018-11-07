<?php
$routes[] = array(
	'name' => 'issue',
	'path' => '/issues/{id:[0-9]+}-{slug}',
	'routes' => array(
		array(
			'name' => 'issue',
			'path' => '',
		),
		array('name' => 'layouts'),
		array(
			'name' => 'layout',
			'path' => '/layouts/{layout}',
		),
		array('name' => 'pussycats'),
	),
);
