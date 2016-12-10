<?php
namespace Website\Action;

use Functional as F;
use Jack\Action\Page;

class Issue extends Page {

	protected function metaTitle() {
		return sprintf('%s | Issue #%d | Cover | Jack Magazine', $this->issue['title'], $this->issue['number']);
	}

	protected static function parseIssueSlug($slug) {
		preg_match('/([0-9]+)-([a-z-]+)/', $slug, $matches);
		return [
			'number' => intval($matches[1]),
			'name' => $matches[2],
		];
	}

	protected function issuePart($issue, $part) {
		return F\map(F\pluck($issue["part_$part"], 'value'), function($item) {
			return cockpit('collections:findOne', 'layouts', ['title' => $item['display']]);
		});
	}

	protected function fetch($number) {
		return cockpit('collections:findOne', 'issues', compact('number'));
	}

	protected function templatePath() {
		return 'issues/single';
	}

	protected function finalize($response) {
		if ($this->data['issue']) return parent::finalize($response);
		return static::notFound();
	}

	protected function fetchData($args) {
		$requested = static::parseIssueSlug($args['slug']);
		$issue = $this->fetch($requested['number']);
		$this->data = array_merge($args, $requested, compact('issue'));
	}

}
