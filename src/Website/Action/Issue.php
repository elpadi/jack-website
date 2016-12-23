<?php
namespace Website\Action;

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
		return sprintf('%s | Issue #%d | Cover | Jack Magazine', $this->data['issue']['title'], $this->data['issue']['number']);
	}

	protected function fetchIssue($slug) {
		return cockpit('collections:findOne', 'issues', compact('slug'));
	}

	protected function finalize($response) {
		if (!isset($this->data['issue'])) return static::notFound($response);
		$this->data['assets'] = array_merge_recursive($this->baseAssets(), $this->assets());
		$this->data['ISSUE_SECTION'] = substr(strtolower(str_replace(__NAMESPACE__, '', get_class($this))), 1);
		return parent::finalize($response);
	}

	protected function fetchData($args) {
		if ($issue = $this->fetchIssue($args['slug'])) {
			$this->data = array_merge($args, compact('issue'));
			$this->data['sections'] = call_user_func_array('array_merge', array_map([$this, 'fetchSections'], [1, 2]));
		}
	}

}
