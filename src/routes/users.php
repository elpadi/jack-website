<?php

$app->get('/user/login', function () use ($site, $app, $view) {
	$app->render('parts/user/login-form.twig', array(
		'nonce' => $site->getService('nonce')->create('login'),
		'destination' => (isset($_GET['destination']) ? $_GET['destination'] : PATH_PREFIX),
		'title' => 'Login',
		'section' => 'user',
		'page' => 'login',
	));
})->setName('login');
$app->post('/user/login', function () use ($site, $app, $view) {
	$post = $app->request->post();
	$error = function($msg) use ($site, $app, $post) {
		$app->flashNow('error', $msg);
		$app->render('parts/user/login-form.twig', array(
			'nonce' => $site->getService('nonce')->create('login'),
			'destination' => (isset($_GET['destination']) ? $_GET['destination'] : PATH_PREFIX),
			'values' => $post,
			'title' => 'Login',
			'section' => 'user',
			'page' => 'login',
		));
	};
	if (!$site->getService('nonce')->check($post['nonce'], 'login')) {
		if (DEBUG) {
			d("Nonces do not match.", $post['nonce']);
		}
		else {
			return $error("Invalid request. Please try again.");
		}
	}
	if (!$site->getService('user')->login($post['username'], $post['password'])) {
		if (DEBUG) {
			d($site->getService('user')->log->getFullConsole());
		}
		else {
			return $error("Invalid username/password combination.");
		}
	}
	$app->redirect($app->urlFor('welcome'));
});
$app->get('/user/logout', function () use ($site, $app, $view) {
	$site->getService('user')->logout();
	$app->flash('info', "You have successfully logged out.");
	$app->redirect(isset($_GET['destination']) ? $_GET['destination'] : PATH_PREFIX);
})->setName('logout');
