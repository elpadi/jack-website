<?php
namespace Website\Action;

use Functional as F;

class Editorial extends Issue {

	protected function templatePath() {
		return 'issues/sections';
	}

	protected function assets() {
		return [
			'css' => ['layouts/synch-scroll','issues/sections'],
			'js' => ['layouts/synch-scroll','issues/sections'],
		];
	}

	protected function metaTitle() {
		return sprintf('Editorial | Issue #%d - %s | Jack Magazine', $this->data['issue']['number'], $this->data['issue']['title']);
	}

	protected function fetchSections($issue, $part) {
		global $app;
		$sections = cockpit('collections:find', sprintf('sections%dx%d', $issue['number'], $part));
		if (empty($sections)) throw new \InvalidArgumentException("No sections found for part $part of issue $issue[number] - $issue[title].", 404);
		foreach ($sections as &$s) $s['url'] = $app->routeLookUp('section', [
			'slug' => $issue['slug'],
			'part' => $part,
			'section' => $s['slug'],
		]);
		return $sections;
	}

	protected function finalize($response) {
		$this->data['assets'] = array_merge_recursive($this->baseAssets(), $this->assets());
		return parent::finalize($response);
	}

	protected function fetchData($args) {
		$issue = $this->fetchIssue($args['slug']);
		$sections = array_merge($this->fetchSections($issue, 1), $this->fetchSections($issue, 2));
		$this->data = array_merge($args, compact('issue','sections'));
	}

}
