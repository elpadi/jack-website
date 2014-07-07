<?php

$can_access_site = curry(array($site, 'checkPermission'), 'access content');

$app->get('/', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/home.twig', array(
		'title' => 'Poster size magazine',
		'first_issue' => $site->getFirstIssue(),
	));
})->setName('home');
$app->get('/about', function () use ($site, $app, $view) {
	$app->render('parts/about.twig', array(
		'title' => 'About',
		'selected_link' => 'About',
	));
})->setName('about');
$app->get('/forbidden', function () use ($site, $app, $view) {
	header('HTTP/1.0 403 Forbidden');
	$app->render('forbidden.twig', array(
		'title' => 'Not authorized',
	));
})->setName('forbidden');
