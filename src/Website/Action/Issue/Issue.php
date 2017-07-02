<?php
namespace Website\Action\Issue;

use Functional as F;
use Jack\Action\Page;
use Website\Issues\Issues as IssuesCollection;

class Issue extends Page {

	protected function assets() {
		$base = [
			'css' => ['sections/sub-nav','issues/'.$this->data['issue']->slug],
			'js' => [],
		];
		return array_merge_recursive($base, $this->issueAssets());
	}

	protected function metaTitle() {
		return sprintf('%s | Jack Magazine', $this->data['issue']->title);
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
