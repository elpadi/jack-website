<?php

$can_access_site = curry(array($site, 'checkPermission'), 'access content');

$app->get('/', function () use ($site, $app, $view) {
	$app->redirect($app->urlFor('welcome'));
})->setName('home');

$app->get('/welcome', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/welcome.twig', array(
		'title' => 'Welcome',
		'first_issue' => $site->getFirstIssue(),
		'section' => 'welcome',
	));
})->setName('welcome');
$app->get('/questions', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/questions.twig', array(
		'title' => 'Questions',
		'section' => 'questions',
		'answers_url' => $app->urlFor('answers'),
	));
})->setName('questions');
$app->get('/answers', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/answers.twig', array(
		'title' => 'Answers',
		'first_issue' => $site->getFirstIssue(),
		'section' => 'answers',
	));
})->setName('answers');
$app->get('/about', function () use ($site, $app, $view) {
	$app->render('parts/about.twig', array(
		'title' => 'About',
		'selected_link' => 'About',
		'section' => 'about',
	));
})->setName('about');
$app->get('/forbidden', function () use ($site, $app, $view) {
	header('HTTP/1.0 403 Forbidden');
	$app->render('forbidden.twig', array(
		'title' => 'Not authorized',
		'section' => 'error',
	));
})->setName('forbidden');
