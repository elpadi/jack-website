<?php
use Website\App;
use Website\Model;
use Website\Issue;
use Website\Layout;
use Website\Poster;

$routes[] = array(
	'path' => '/admin/issues',
	'routes' => array(
		array(
			'name' => 'list-issues',
			'method' => 'get',
			'path' => '',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('admin/issues/list', ['issues' => Model::all('issue')]));
			},
		),
		array(
			'name' => 'add-issue',
			'method' => 'get',
			'path' => '/add',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('admin/issues/add'));
			},
		),
		array(
			'name' => 'issue-form-handler',
			'method' => 'post',
			'path' => '/issue',
			'action' => function($request, $response, $args) {
				Issue::handleSubmission($_POST);
				return $response->withRedirect(App::routeLookup('list-issues'));
			},
		),
		array(
			'name' => 'edit-issue',
			'method' => 'get',
			'path' => '/issue/{issue_id}',
			'action' => function($request, $response, $args) {
				$issue = Model::byId('issue', $args['issue_id']);
				return $response->write(App::render('admin/issues/issue-form', [
					'issue' => $issue,
					'layouts' => $issue->getLayouts(),
				]));
			},
		),
		array(
			'name' => 'add-layout',
			'method' => 'get',
			'path' => '/{slug}/layout',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('admin/issues/layout-form', [
					'issue' => Model::bySlug('issue', $args['slug']),
					'layout' => Model::create('layout'),
					'sections' => Model::all('section'),
				]));
			},
		),
		array(
			'name' => 'edit-layout',
			'method' => 'get',
			'path' => '/layout/{layout_id}',
			'action' => function($request, $response, $args) {
				$layout = Model::byId('layout', $args['layout_id']);
				return $response->write(App::render('admin/issues/layout-form', [
					'layout' => $layout,
					'issue' => $layout->getIssue(),
					'section' => $layout->getSection(),
					'sections' => Model::all('section'),
					'posters' => $layout->getPosters(),
				]));
			},
		),
		array(
			'name' => 'layout-form-handler',
			'method' => 'post',
			'path' => '/layout',
			'action' => function($request, $response, $args) {
				Layout::handleSubmission($_POST);
				return $response->withRedirect(App::routeLookup('edit-issue', ['slug' => Model::byId('issue', $_POST['issue_id'])->getSlug()]));
			},
		),
		array(
			'name' => 'add-poster',
			'method' => 'get',
			'path' => '/layout/{layout_id}/poster',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('admin/issues/poster-form', [
					'layout' => Model::byId('layout', $args['layout_id']),
					'poster' => Model::create('poster'),
				]));
			},
		),
		array(
			'name' => 'edit-poster',
			'method' => 'get',
			'path' => '/poster/{poster_id}',
			'action' => function($request, $response, $args) {
				$poster = Model::byId('poster', $args['poster_id']);
				return $response->write(App::render('admin/issues/poster-form', [
					'poster' => $poster,
					'layout' => $poster->getLayout(),
				]));
			},
		),
		array(
			'name' => 'poster-form-handler',
			'method' => 'post',
			'path' => '/poster',
			'action' => function($request, $response, $args) {
				Poster::handleSubmission($_POST);
				return $response->withRedirect(App::routeLookup('edit-layout', ['layout_id' => $_POST['layout_id']]));
			},
		),
	),
	'middleware' => array(
		function ($request, $response, $next) {
			return App::userCan('edit issues') ? $next($request, $response) : App::notAuthorized($response);
		}
	),
);
