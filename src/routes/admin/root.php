<?php

$can_access_admin = curry(array($site, 'checkPermission'), 'access admin');

$app->get('/admin', $can_access_admin, function () use ($site, $app, $view) {
	$app->render('admin/parts/home.twig', array(
		'sections' => $site->getAdminSections('Dashboard'),
	));
})->setName('admin/home');
