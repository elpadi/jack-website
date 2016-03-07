<?php

\Jack\App::$framework->group('/admin/issues', function() {
	
	$slim = \Jack\App::$framework;

	$slim->get('/{slug}', function ($request, $response, $args) {
		try {
			$issue = new \Jack\Issue($args['slug']);
		}
		catch (\Exception $e) {
			return Jack\App::notFound($response, $e);
		}
		return $response->write(\Jack\App::template('admin/parts/issues/issue', $issue));
	})->setName('edit-issue');

})->add(function ($request, $response, $next) {
	
	if (!\Jack\App::userCan('edit issues')) return
		$response->withStatus(403);
	return $next($request, $response);

});

/*

$can_edit_issues = curry(array($site, 'checkPermission'), 'edit issues');

$app->get('/admin/issues', $can_edit_issues, function () use ($site, $app, $view) {
	$app->render('admin/parts/issues/issues.twig', array(
		'title' => 'Issues',
		'issues' => $site->getIssues(),
		'section' => 'issues',
		'page' => 'issues',
	));
})->setName('admin/issues');
$app->post('/admin/issues/:slug', $can_edit_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	try {
		$issue->update($app->request->post(), $site, $site);
		$app->flash('info', "The issue '$issue->title' was successfully updated.");
	}
	catch (\Exception $e) {
		$app->flash('error', "Error updating the issue. ".$e->getFile().':'.$e->getLine().'  '.$e->getMessage());
	}
	$app->redirect($app->urlFor('admin/issue', array('slug' => $issue->slug)));
})->setName('admin/issue/update');
$app->post('/admin/issues/:slug/images', $can_edit_issues, function ($slug) use ($site, $app, $view) {
	$app->response->headers->set('Content-Type', 'application/json');
	$issue = $site->getIssueBySlug($slug);
	try {
		$issue->updateImages($_FILES, $site);
		echo json_encode(array('success' => true, 'issue' => $issue));
	}
	catch (\Exception $e) {
		echo json_encode(array('success' => false, 'error' => $e->getFile().':'.$e->getLine().'  '.$e->getMessage()));
	}
})->setName('update-images');

$app->get('/admin/issues/:slug/posters', $can_edit_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('admin/parts/issues/posters.twig', array(
		'title' => $view->get('title').' | Edit order '.$issue->title,
		'issue' => $issue,
		'posters' => $posters,
		'section' => 'issues',
		'page' => 'issue-posters',
	));
})->setName('edit-posters');
$app->post('/admin/issues/:slug/posters', $can_edit_issues, function ($slug) use ($site, $app, $view) {
	$app->response->headers->set('Content-Type', 'application/json');
	$issue = $site->getIssueBySlug($slug);
	try {
		$issue->updatePosterOrder($app->request->post(), $site);
		echo json_encode(array('success' => true));
	}
	catch (\Exception $e) {
		echo json_encode(array('success' => false, 'error' => $e->getFile().':'.$e->getLine().'  '.$e->getMessage()));
	}
});
$app->get('/admin/issues/:slug/posters/add', $can_edit_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$app->render('admin/parts/add_page.twig', array(
		'title' => $view->get('title').' | Add poster to '.$issue->title,
		'issue' => $site->getIssueBySlug($slug),
		'section' => 'issues',
		'page' => 'add-poster',
	));
})->setName('add-poster');
$app->post('/admin/issues/:slug/posters/add', $can_edit_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$poster = new Jack\Poster();
	$poster->issueId = $issue->id;
	try {
		$poster->update($app->request->post(), $_FILES, $site, $site);
		$app->flash('info', "The poster '$poster->title' was added to this issue.");
	}
	catch (\Exception $e) {
		if (DEBUG) {
			d($e->getFile().':'.$e->getLine().' - '.$e->getMessage(), $e, $issue, $poster);
		}
		else {
			echo "Adding poster failed.";
		}
	}
	$app->redirect($app->urlFor('admin/issue/pages', array('slug' => $slug)));
});
$app->post('/admin/issues/poster/delete/:id', $can_edit_issues, function ($id) use ($site, $app, $view) {
	$app->response->headers->set('Content-Type', 'application/json');
	$poster = $site->getPosterById($id);
	try {
		$poster->delete($site, $site);
		echo json_encode(array('success' => true));
	}
	catch (\Exception $e) {
		echo json_encode(array('success' => false, 'error' => $e->getFile().':'.$e->getLine().'  '.$e->getMessage()));
	}
})->setName('delete-poster');
 */
