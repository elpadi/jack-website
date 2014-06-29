<?php

$app->get('/', array($site, 'requireLogin'), function () use ($site, $app, $view) {
	$app->render('parts/home.twig', array(
		'title' => $view->get('title') . ' | Poster size magazine',
		'section' => 'home',
		'first_issue' => $site->getFirstIssue(),
	));
})->setName('home');
