<?php
use Website\App;
use Website\Issue;

$routes[] = array(
	'path' => '/issues',
	'routes' => array(
		array(
			'name' => 'issue',
			'method' => 'get',
			'path' => '/{slug}',
			'action' => function($request, $response, $args) {
				try {
					if ($issue = Issue::bySlug($args['slug'])) return $response->write(App::template('issue', ['issue' => $issue]));
				}
				catch (Exception $e) {
					return App::notFound($response, $e);
				}
				return App::notFound($response);
			},
		),
	),
	'middleware' => array(
	),
);
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
