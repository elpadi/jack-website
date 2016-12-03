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
		/*
		array(
			'name' => 'intro',
			'action' => function($request, $response, $args) {
				App::setIntroAsSeen();
				return $response->write(App::render('intro', ['bodyClass' => sprintf('%s-intro', App::hasSeenIntro() ? 'no' : 'has')]));
			},
		),
		 */
		array('name' => 'intro'),
		array('name' => 'about'),
		array('name' => 'contact'),
		array('name' => 'event'),
	),
);
