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
		'page' => 'welcome',
	));
})->setName('welcome');
$app->get('/questions', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/questions.twig', array(
		'title' => 'Questions',
		'section' => 'welcome',
		'page' => 'questions',
		'answers_url' => $app->urlFor('answers'),
	));
})->setName('questions');
$app->get('/answers', $can_access_site, function () use ($site, $app, $view) {
	$app->render('parts/answers.twig', array(
		'title' => 'Answers',
		'first_issue' => $site->getFirstIssue(),
		'section' => 'welcome',
		'page' => 'answers',
	));
})->setName('answers');
$app->get('/add-shader', $can_access_site, function () use ($site, $app, $view) {
	$shaderDir = TEMPLATE_DIR.'/shaders/'.$_GET['s'];
	if (!is_dir($shaderDir)) {
		$app->pass();
	}
	$app->response->headers->set('Content-Type', 'text/javascript');
	echo 'define([], function() { return {'.
		'uniforms: '.str_replace("\n", '', file_get_contents("$shaderDir/uniforms.js")).','.
		'attributes: '.str_replace("\n", '', file_get_contents("$shaderDir/attributes.js")).','.
		'vertexShader: "'.str_replace("\n", '', file_get_contents("$shaderDir/vertex.glsl")).'",'.
		'fragmentShader: "'.str_replace("\n", '', file_get_contents("$shaderDir/fragment.glsl")).'"'.
	'}; });';
})->setName('shader');
