<?php
$routes[] = array(
	'name' => 'ImageManager',
	'path' => '/image-manager',
	'routes' => array(
		array(
			'name' => 'index',
			'path' => '',
		),
		array(
			'name' => 'listing',
		),
		array(
			'name' => 'info',
			'path' => '/{size}/{path}',
		),
		array(
			'name' => 'delete',
			'path' => '/delete/{hash}',
		),
	),
);
