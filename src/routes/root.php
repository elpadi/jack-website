<?php

$can_access_site = curry(array($site, 'checkPermission'), 'access content');

$app->get('/', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/home.twig', array(
		'title' => $view->get('title') . ' | Poster size magazine',
		'section' => 'home',
		'first_issue' => $site->getFirstIssue(),
	));
})->setName('home');
$app->get('/forbidden', function () use ($site, $app, $view) {
	header('HTTP/1.0 403 Forbidden');
	$app->render('forbidden.twig', array(
		'title' => 'Not authorized',
	));
})->setName('forbidden');
