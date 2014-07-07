<?php

$can_access_issues = curry(array($site, 'checkPermission'), 'access issues');

$app->get('/issues', $can_access_issues, function () use ($site, $app, $view) {
	$app->render('parts/issues.twig', array(
		'title' => 'Issues',
		'selected_link' => 'Issues',
		'issues' => $site->getIssues(),
	));
})->setName('issues');
$app->get('/issues/:slug', $can_access_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('parts/issue.twig', array(
		'title' => $issue->title.' Issue',
		'selected_link' => 'Issues',
		'issue' => $issue,
		'posters' => $posters,
	));
})->setName('issue');
