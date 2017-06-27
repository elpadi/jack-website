<?php
namespace Website\Action\Issues;

use Functional as F;
use Jack\Action\Page;

abstract class Issue extends Page {

	final protected function baseAssets() {
		return [
			'css' => ['sections/sub-nav'],
			'js' => [],
		];
	}

	protected function metaTitle() {
		return sprintf('%s | Issue #%d | Jack Magazine', $this->data['issue']['title'], $this->data['issue']['number']);
	}

	protected function fetchIssue($slug) {
		$issue = cockpit('collections:findOne', 'issues', compact('slug'));
		if (!$issue) throw new \InvalidArgumentException("Issue '$slug' not found.", 404);
		return $issue;
	}

	protected function finalize($response) {
		$this->data['assets'] = array_merge_recursive($this->baseAssets(), $this->assets());
		$this->data['ISSUE_SECTION'] = substr(strtolower(str_replace(__NAMESPACE__, '', get_class($this))), 1);
		return parent::finalize($response);
	}

	protected function fetchData($args) {
		$this->data = array_merge($args, ['issue' => $this->fetchIssue($args['slug'])]);
	}

}
