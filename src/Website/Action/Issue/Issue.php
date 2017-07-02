<?php
namespace Website\Action\Issue;

use Functional as F;
use Jack\Action\Page;
use Website\Issues\Issues as IssuesCollection;

class Issue extends Page {

	protected function assets() {
		$base = [
			'css' => ['sections/sub-nav'],
			'js' => [],
		];
		return array_merge_recursive($base, $this->issueAssets());
	}

	protected function issueAssets() {
		switch ($this->data['issue']->id) {
		case 1:
		case 2:
			return [
				'css' => ['issues/sections'],
				'js' => ['issues/sections'],
			];
		case 3:
			var_dump(__FILE__.":".__LINE__." - ".__METHOD__, 'overview');
			exit(0);
		}
	}

	protected function metaTitle() {
		return sprintf('%s | Jack Magazine', $this->data['issue']->title);
	}

	protected function templatePath() {
		switch ($this->data['issue']->id) {
		case 1:
		case 2:
			return 'issues/sections';
		case 3:
			var_dump(__FILE__.":".__LINE__." - ".__METHOD__, 'overview');
			exit(0);
			return 'issues/overview';
		}
	}

	protected function fetchData($args) {
		$issues = new IssuesCollection();
		extract($args);
		$issues->fetchById($id);
		$issue = $issues->current();
		if (!$issue || $issue->slug !== $slug) throw new \InvalidArgumentException("Issue '$id-$slug' not found.", 404);
		$this->data['issue'] = $issue;
	}

}
