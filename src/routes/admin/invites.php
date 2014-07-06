<?php

$can_edit_invites = curry(array($site, 'checkPermission'), 'edit invites');

$app->get('/admin/invites', $can_edit_invites, function () use ($site, $app, $view) {
	$app->render('admin/parts/invites.twig', array(
		'title' => $view->get('title').' | Invites',
		'sections' => $site->getAdminSections('Invites'),
		'invites' => $site->getInvites(),
	));
})->setName('admin/invites');
$app->get('/admin/invites/send', $can_edit_invites, function () use ($site, $app, $view) {
	$id = $app->request->get('id');
	$invite = $id ? $site->getInviteById($id) : new Jack\Invite();
	$app->render('admin/parts/invites/send.twig', array(
		'title' => $view->get('title').' | Send Invite',
		'sections' => $site->getAdminSections('Invites'),
		'invite' => $invite,
	));
})->setName('admin/send-invite');
$app->post('/admin/invites/send', $can_edit_invites, function () use ($site, $app, $view) {
	$invite = new Jack\Invite();
	try {
		$invite->setData($app->request->post());
		$invite->save($site);
		$invite->hydrate($site, $site);
		$invite->send($site, $site, $site);
	}
	catch (\Exception $e) {
		echo "Invite not sent.";
		if (DEBUG) {
			echo ' --- '.$e->getFile().':'.$e->getLine().' - '.$e->getMessage();
			exit(1);
		}
	}
	$app->flash('info', "Invite sent to $invite->email.");
	$app->redirect($app->urlFor('admin/invites'));
});

