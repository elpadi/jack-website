<?php
use Website\App;
use Website\AssetManager;
use Website\Model;
use Website\Issue;

$routes[] = array(
	'path' => '/issues',
	'routes' => array(
		array(
			'name' => 'issues',
			'path' => '',
			'action' => function($request, $response, $args) {
				return $response->withRedirect(App::routeLookup('issue', ['slug' => '1-dare-to-dream']));
			},
		),
		array(
			'name' => 'issue',
			'path' => '/{slug}',
			'action' => function($request, $response, $args) {
				return $response->write(App::render('issues/single', ['issue' => Model::bySlug('issue', $args['slug'])]));
			},
		),
		array(
			'name' => 'editorial',
			'path' => '/{slug}/editorial-{part}',
			'action' => function($request, $response, $args) {
				$issue = Model::bySlug('issue', $args['slug']);
				return $response->write(App::render('issues/editorial', [
					'issue' => $issue,
					'sections' => Issue::getSections($issue->getNumber(), $args['part']),
					'asset_path' => AssetManager::getAssetsDir().sprintf('/issue-%d/layouts/part-%d/4', $issue->getNumber(), $args['part']),
				]));
			},
		),
		array(
			'name' => 'editorial',
			'path' => '/{issue}/editorial-{part}/{layout}',
			'action' => function($request, $response, $args) {
				$issue = Model::bySlug('issue', $args['issue']);
				return $response->write(App::render('issues/layout', ['issue' => $issue, 'layout' => Issue::getLayout($issue->getNumber(), $args['part'], $args['layout'])]));
			},
		),
	),
	'middleware' => array(
		function ($request, $response, $next) {
			$args = $request->getAttribute('routeInfo')[2];
			try {
				if (isset($args['part']) && !in_array($args['part'], ['1','2'])) throw new InvalidArgumentException("Issue part not found.");
				if (isset($args['slug'])) Model::bySlug('issue', $args['slug']);
			}
			catch (Exception $e) {
				return App::notFound($response, $e);
			}
			return $next($request, $response);
		}
	),
);
