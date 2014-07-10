<?php

$app->get('/user/login', function () use ($site, $app, $view) {
	$app->render('parts/user/login-form.twig', array(
		'nonce' => $site->getService('nonce')->create('login'),
		'destination' => (isset($_GET['destination']) ? $_GET['destination'] : '/'),
		'title' => 'Login',
		'section' => 'user',
	));
})->setName('login');
$app->post('/user/login', function () use ($site, $app, $view) {
	$post = $app->request->post();
	$error = function($msg) use ($site, $app) {
		$app->flash('error', $msg);
		$app->render('parts/user/login-form.twig', array(
			'nonce' => $site->getService('nonce')->create('login'),
			'destination' => (isset($_GET['destination']) ? $_GET['destination'] : '/'),
			'values' => $post,
			'title' => 'Login',
			'section' => 'user',
		));
	};
	if (!$site->getService('nonce')->check($post['nonce'], 'login')) {
		return $error("Invalid request. Please try again.");
	}
	if (!$site->getService('user')->login($post['username'], $post['password'])) {
		return $error("Invalid username/password combination.");
	}
	$app->redirect($post['destination']);
});
