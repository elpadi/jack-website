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
	$user = $site->getService('user');
	$post = $app->request->post();
	$errors = array();
	if (!$site->getService('nonce')->check($post['nonce'], 'login')) {
		$errors[] = DEBUG ? "Nonce '$post[nonce]' does not match." : "Invalid request. Please try again.";
	}
	if (!$user->login($post['username'], $post['password'])) {
		foreach (array_merge($user->log->getErrors(), array_values($user->log->getFormErrors())) as $error) $errors[] = $error;
	}
	if (count($errors) > 0) {
		$app->render('parts/user/login-form.twig', array(
			'nonce' => $site->getService('nonce')->create('login'),
			'destination' => (isset($_GET['destination']) ? $_GET['destination'] : PATH_PREFIX),
			'title' => 'Login',
			'section' => 'user',
			'page' => 'login',
			'errors' => array_unique($errors),
			'values' => $post,
		));
	}
	else {
		$app->redirect($app->urlFor('welcome'));
	}
});
$app->get('/user/logout', function () use ($site, $app, $view) {
	$site->getService('user')->logout();
	$app->flash('info', "You have successfully logged out.");
	$app->redirect($app->urlFor('login'));
})->setName('logout');
