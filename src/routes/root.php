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
		array(
			'name' => 'intro',
			'action' => function($request, $response, $args) {
				App::setIntroAsSeen();
				return $response->write(App::render('intro', ['bodyClass' => sprintf('%s-intro', App::hasSeenIntro() ? 'no' : 'has')]));
			},
		),
		array('name' => 'about'),
		array('name' => 'contact'),
		array(
			'method' => 'post',
			'path' => 'contact',
			'name' => 'contact-message',
			'action' => function($request, $response, $args) {
				$sent = true || App::contactEmail();
				return $response->write(App::render('contact', ['sent' => $sent]));
			},
		),
		array('name' => 'event', 'vars' => array(
			'images' => array_map(function($path) { return str_replace(WEBSITE_DIR.'/assets/', '', $path); }, glob(WEBSITE_DIR.'/assets/event/*.jpg')),
		)),
	),
	'middleware' => array(
	),
);
