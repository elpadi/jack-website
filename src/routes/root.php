<?php

$can_access_site = curry(array($site, 'checkPermission'), 'access content');

$app->get('/', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/home.twig', array(
		'title' => 'Poster size magazine',
		'first_issue' => $site->getFirstIssue(),
		'section' => 'home',
	));
})->setName('home');
$app->get('/welcome', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/introduction.twig', array(
		'title' => 'Welcome',
		'section' => 'home',
	));
})->setName('welcome');
$app->get('/questions', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/questions.twig', array(
		'title' => 'Questions',
		'section' => 'home',
		'answers_url' => $app->urlFor('answers'),
	));
})->setName('questions');
$app->get('/answers', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/answers.twig', array(
		'title' => 'Answers',
		'section' => 'home',
		'images' => array(
			'why_a' => $site->asset('images/unsq_a.jpg'),
			'why_b' => $site->asset('images/unsq_b.jpg'),
		),
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
