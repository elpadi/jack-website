<?php

$can_access_issues = curry(array($site, 'checkPermission'), 'access issues');

$app->get('/issues', $can_access_issues, function () use ($site, $app, $view) {
	$app->render('parts/issues.twig', array(
		'title' => $view->get('title').' | Issues',
		'issues' => $site->getIssues(),
		'section' => 'issues',
	));
})->setName('issues');
$app->get('/issues/:slug', $can_access_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('parts/issue.twig', array(
		'title' => $view->get('title').' | '.$issue->title,
		'issue' => $issue,
		'posters' => $posters,
		'section' => 'issue',
	));
})->setName('issue');
