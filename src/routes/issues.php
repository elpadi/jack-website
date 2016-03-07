<?php
/*
$can_access_issues = curry(array($site, 'checkPermission'), 'access content');

$app->get('/issues', $can_access_issues, function () use ($site, $app, $view) {
	$app->render('parts/issues.twig', array(
		'title' => 'Issues',
		'selected_link' => 'Issues',
		'section' => 'issues',
		'page' => 'issues',
		'issues' => $site->getIssues(),
	));
})->setName('issues');
$app->get('/issues/:slug', $can_access_issues, function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('parts/issue.twig', array(
		'title' => $issue->title.' Issue',
		'selected_link' => 'Issues',
		'section' => 'issue',
		'page' => $slug,
		'issue' => $issue,
		'posters' => $posters,
	));
})->setName('issue');
 */
