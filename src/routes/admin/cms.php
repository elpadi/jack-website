<?php

//$can_edit_cms = curry(array($site, 'checkPermission'), 'edit cms');
$can_edit_cms = function() { return true; };

$app->get('/admin/cms', $can_edit_cms, function () use ($site, $app, $view) {
	$app->render('admin/parts/cms/cms.twig', array(
		'title' => $view->get('title').' | Content',
		'pages' => array(
			array(
				'name' => 'answers',
				'editUrl' => $app->urlFor('stories'),
			),
		),
		'section' => 'cms',
		'page' => 'cms',
	));
})->setName('admin/cms');
$app->get('/admin/cms/stories', $can_edit_cms, function () use ($site, $app, $view) {
	$stories = unserialize(file_get_contents(ROOT_DIR.'/site/cms/stories.php'));
	$app->render('admin/parts/cms/stories.twig', array(
		'title' => $view->get('title').' | Edit the story of Jack',
		'stories' => $stories,
		'colors' => array('delta','quill-gray'),
		'section' => 'cms',
		'page' => 'stories',
	));
})->setName('stories');
$app->post('/admin/cms/stories', $can_edit_cms, function () use ($site, $app, $view) {
	$data = $app->request->post();
	file_put_contents(ROOT_DIR.'/site/cms/stories.php', serialize($data['stories']));
	$app->flashNow('info', "The stories were saved");
	$app->render('admin/parts/cms/stories.twig', array(
		'title' => $view->get('title').' | Edit the story of Jack',
		'stories' => $data['stories'],
		'colors' => array('delta','quill-gray'),
		'section' => 'cms',
		'page' => 'stories',
	));
});
/*
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
})->setName('admin/issue/update-images');

$app->get('/admin/issues/:slug/pages', $can_edit_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('admin/parts/pages.twig', array(
		'title' => $view->get('title').' | Edit order '.$issue->title,
		'issue' => $issue,
		'posters' => $posters,
		'section' => 'issues',
		'page' => 'issue-posters',
	));
})->setName('admin/issue/pages');
$app->post('/admin/issues/:slug/pages', $can_edit_issues, function ($slug) use ($site, $app, $view) {
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
$app->get('/admin/issues/:slug/pages/add', $can_edit_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$app->render('admin/parts/add_page.twig', array(
		'title' => $view->get('title').' | Add poster to '.$issue->title,
		'issue' => $site->getIssueBySlug($slug),
		'section' => 'issues',
		'page' => 'add-poster',
	));
})->setName('admin/issue/newpage');
$app->post('/admin/issues/:slug/pages/add', $can_edit_issues, function ($slug) use ($site, $app, $view) {
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
})->setName('admin/delete-poster');
*/
