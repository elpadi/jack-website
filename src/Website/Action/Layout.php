<?php
namespace Website\Action;

use Functional as F;

class Layout extends Issue {

	protected function templatePath() {
		return 'issues/layout';
	}

	protected function assets() {
		return [
			'css' => ['issues/layout'],
			'js' => [],
		];
	}

	public function fetchLayout($issue, $slug) {
		global $app;
		$layout = cockpit('collections:findOne', sprintf('layouts%d', $issue['number']), compact('slug'));
		$layout['src'] = $app->imageManager->imageUrl($app->url($layout['image']['path']), 'medium');
		$layout['srcset'] = $app->imageManager->responsiveImageSrcset($app->url($layout['image']['path']), ['medium','large']);
		return $layout;
	}

	protected function metaTitle() {
		return sprintf('%s | Issue #%d - %s | Jack Magazine', $this->data['layout']['title'], $this->data['issue']['number'], $this->data['issue']['title']);
	}

	protected function fetchData($args) {
		if (($issue = $this->fetchIssue($args['slug'])) && ($layout = $this->fetchLayout($issue, $args['layout']))) {
			$this->data = array_merge($args, compact('issue','layout'));
		}
	}

}