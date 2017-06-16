<?php
use Website\App;

$routes[] = array(
	'path' => '/',
	'routes' => array(
		array(
			'name' => 'home',
			'path' => '',
		),
		array('name' => 'intro'),
		array('name' => 'jbpc'),
		array('name' => 'about'),
		array('name' => 'contact'),
		array('name' => 'event'),
		array('name' => 'special-project'),
	),
);
