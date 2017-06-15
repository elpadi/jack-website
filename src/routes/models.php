<?php
$routes[] = array(
	'name' => 'models',
	'path' => '/models',
	'routes' => array(
		array(
			'name' => 'models',
			'path' => '',
		),
		array(
			'name' => 'model',
			'path' => '/{slug}',
		),
	),
);
