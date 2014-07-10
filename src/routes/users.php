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
	$error = function($msg) use ($site, $app, $post) {
		$app->flashNow('error', $msg);
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
$app->get('/user/logout', function () use ($site, $app, $view) {
	$site->getService('user')->logout();
	$app->flash('info', "You have successfully logged out.");
	$app->redirect(isset($_GET['destination']) ? $_GET['destination'] : '/');
})->setName('logout');
