<?php

$app->get('/admin/issues', array($site, 'requireAdmin'), function () use ($site, $app, $view) {
	$app->render('admin/parts/issues.twig', array(
		'title' => $view->get('title').' | Issues',
		'issues' => $site->getIssues(),
	));
})->setName('admin/issues');
$app->get('/admin/issues/:slug', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	try {
		$issue = $site->getIssueBySlug($slug);
	}
	catch (\Exception $e) {
		if ($e->getCode() === Jack\Site::E_NOT_FOUND) {
			return $app->notFound();
		}
		echo $e->getFile().':'.$e->getLine().'  '.$e->getMessage();
		exit(0);
	}
	$app->render('admin/parts/issue.twig', array(
		'title' => $view->get('title').' | Edit '.$issue->title,
		'issue' => $issue,
		'sections' => $site->getAdminSections('Issues'),
	));
})->setName('admin/issue');
$app->post('/admin/issues/:slug', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
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
$app->post('/admin/issues/:slug/images', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$app->response->headers->set('Content-Type', 'application/json');
	$issue = $site->getIssueBySlug($slug);
	try {
		$issue->updateImages($_FILES, $site);
		echo json_encode(array('success' => true, 'issue' => $issue));
	}
	catch (\Exception $e) {
		echo json_encode(array('success' => false, 'error' => $e->getFile().':'.$e->getLine().'  '.$e->getMessage()));
	}
})->setName('admin/issue/update-images');

$app->get('/admin/issues/:slug/pages', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('admin/parts/pages.twig', array(
		'title' => $view->get('title').' | Edit order '.$issue->title,
		'sections' => $site->getAdminSections('Issues'),
		'issue' => $issue,
		'posters' => $posters,
	));
})->setName('admin/issue/pages');
$app->post('/admin/issues/:slug/pages', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
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
$app->get('/admin/issues/:slug/pages/add', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$app->render('admin/parts/add_page.twig', array(
		'title' => $view->get('title').' | Add poster to '.$issue->title,
		'issue' => $site->getIssueBySlug($slug),
		'sections' => $site->getAdminSections('Issues'),
	));
})->setName('admin/issue/newpage');
$app->post('/admin/issues/:slug/pages/add', array($site, 'requireAdmin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$poster = new Jack\Poster();
	$poster->issueId = $issue->id;
	try {
		$poster->update($app->request->post(), $_FILES, $site, $site);
		$app->flash('info', "The poster '$poster->title' was added to this issue.");
		$app->redirect($app->urlFor('admin/issue', array('slug' => $slug)));
	}
	catch (\Exception $e) {
		echo "Adding poster failed.";
		if (DEBUG) {
			echo ' --- '.$e->getFile().':'.$e->getLine().' - '.$e->getMessage();
		}
	}
});
$app->post('/admin/issues/poster/delete/:id', array($site, 'requireAdmin'), function ($id) use ($site, $app, $view) {
	$app->response->headers->set('Content-Type', 'application/json');
	$poster = $site->getPosterById($id);
	try {
		$poster->delete($site, $site);
		echo json_encode(array('success' => true));
	}
	catch (\Exception $e) {
		echo json_encode(array('success' => false, 'error' => $e->getFile().':'.$e->getLine().'  '.$e->getMessage()));
	}
})->setName('admin/delete-poster');

