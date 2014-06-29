<?php

$app->get('/admin/users', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/users.twig', array(
		'sections' => $site->getAdminSections('Users'),
	));
})->setName('admin/users');
$app->get('/admin/users/create', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/user-add.twig', array(
		'sections' => $site->getAdminSections('Users'),
	));
})->setName('admin/create-user');
$app->post('/admin/users/create', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$user = new Jack\User();
	try {
		$user->setData($app->request->post());
		$user->save($site);
	}
	catch (\Exception $e) {
		echo "User not saved.";
		if (DEBUG) {
			echo ' --- '.$e->getFile().':'.$e->getLine().' - '.$e->getMessage();
			exit(1);
		}
	}
	$app->flash('info', "User '$user->username' saved.");
	$app->redirect($app->urlFor('admin/users'));
});

