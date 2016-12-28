<?php
use Website\App;

$routes[] = array(
	'path' => '/',
	'routes' => array(
		array(
			'name' => 'home',
			'path' => '',
			'action' => 'intro',
		),
		array('name' => 'intro'),
		array('name' => 'about'),
		array('name' => 'contact'),
		array('name' => 'event'),
	),
);
