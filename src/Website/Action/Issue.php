<?php
namespace Website\Action;

use Functional as F;
use Jack\Action\Page;

class Issue extends Page {

	protected function baseAssets() {
		return [
			'css' => ['sections/sub-nav'],
			'js' => [],
		];
	}

	protected function assets() {
		return [
			'css' => ['layouts/synch-scroll','issues/sections'],
			'js' => ['layouts/synch-scroll','issues/sections'],
		];
	}

	protected function metaTitle() {
		return sprintf('%s | Issue #%d | Cover | Jack Magazine', $this->data['issue']['title'], $this->data['issue']['number']);
	}

	protected function fetchSections($part) {
		global $app;
		$sections = cockpit('collections:find', sprintf('sections%dx%d', $this->data['issue']['number'], $part));
		foreach ($sections as &$s) $s['url'] = $app->routeLookUp('section', [
			'slug' => $this->data['issue']['slug'],
			'part' => $part,
			'section' => $s['slug'],
		]);
		return $sections;
	}

	protected function fetchIssue($slug) {
		return cockpit('collections:findOne', 'issues', compact('slug'));
	}

	protected function templatePath() {
		return 'issues/sections';
	}

	protected function finalize($response) {
		if (!isset($this->data['issue'])) return static::notFound($response);
		$this->data['assets'] = array_merge_recursive($this->baseAssets(), $this->assets());
		return parent::finalize($response);
	}

	protected function fetchData($args) {
		if ($issue = $this->fetchIssue($args['slug'])) {
			$this->data = array_merge($args, compact('issue'));
			$this->data['sections'] = call_user_func_array('array_merge', array_map([$this, 'fetchSections'], [1, 2]));
		}
	}

}
