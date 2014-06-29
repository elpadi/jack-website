<?php

$app->get('/admin', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/home.twig', array(
		'sections' => $site->getAdminSections('Dashboard'),
	));
})->setName('admin/home');
