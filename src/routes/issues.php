<?php

$app->get('/issues', array($site, 'requireLogin'), function () use ($site, $app, $view) {
	$app->render('parts/issues.twig', array(
		'title' => $view->get('title').' | Issues',
		'issues' => $site->getIssues(),
		'section' => 'issues',
	));
})->setName('issues');
$app->get('/issues/:slug', array($site, 'requireLogin'), function ($slug) use ($site, $app, $view) {
	$issue = $site->getIssueBySlug($slug);
	$posters = $site->getPostersByIssueId($issue->id);
	$app->render('parts/issue.twig', array(
		'title' => $view->get('title').' | '.$issue->title,
		'issue' => $issue,
		'posters' => $posters,
		'section' => 'issue',
	));
})->setName('issue');
