<?php

$app->get('/user/login', function () use ($app, $view) {
	$app->render('parts/user/login-form.twig', array(
		'nonce' => \ulNonce::Create('login'),
		'destination' => (isset($_GET['destination']) ? $_GET['destination'] : '/'),
		'email' => isset($_GET['email']) ? $_GET['email'] : '',
		'title' => $view->get('title') . ' | Login',
		'section' => 'login',
	));
})->setName('login');
$app->post('/user/login', array($site, 'actionLogin'));
