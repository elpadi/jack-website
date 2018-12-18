<?php
namespace Website\Action\Issue;

use Functional as F;
use Website\App;
use Website\Issues\Layouts;

class Layout extends Issue {

	protected function templatePath() {
		return 'issues/layout';
	}

	protected function issueAssets() {
		return [
			'css' => ['layouts/full-width','issues/layout'],
			'js' => ['issues/layout'],
		];
	}

	protected function graphTags() {
		$tags = parent::graphTags();
		$tags['OPEN_GRAPH']['image'] = App::canonicalUrl($this->data['layout']->getImageSrc());
		$tags['TWITTER_CARD']['card'] = 'summary_large_image';
		return $tags;
	}

	protected function metaTitle() {
		return sprintf('%s | %s', $this->data['layout']->title, parent::metaTitle());
	}

	protected function fetchData($args) {
		parent::fetchData($args);
		$this->data['layout'] = Layouts::getBySlug($this->data['issue']->id, $args['layout']);
	}

}
