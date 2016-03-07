<?php
/*

$app->get('/invite/confirmation', function () use ($site, $app, $view) {
	$app->render('parts/invites/confirmation.twig', array(
		'section' => 'invite-confirmation',
	));
})->setName('invite/confirmation');
$app->get('/invite/:hash', function ($hash) use ($invite_config, $site, $app, $view) {
	if ($site->isUserLoggedIn()) { 
		$app->redirect($app->urlFor('home'));
	}
	$invite = $site->getInviteByHash($hash);
	if (!$invite) {
		$app->notFound();
	}
	try {
		$invite->hydrate($site, $site);
		$invite->recordUse($site);
		$user = new Jack\User();
		$user->setData($invite_config['user']);
		$user->login();
	}
	catch (\Exception $e) {
		echo "Could not log in.";
		if (DEBUG) {
			echo ' --- '.$e->getFile().':'.$e->getLine().' - '.$e->getMessage();
		}
		exit(1);
	}
	$app->redirect($app->urlFor(empty($invite->uses) ? 'invite/confirmation' : 'home'));
})->setName('invite');
 */
