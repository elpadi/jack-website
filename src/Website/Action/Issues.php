<?php
namespace Website\Action\Issues;

use Functional as F;
use Jack\Action\Page;
use Website\Issues\Issues as IssuesCollection;

class Issues extends Page {

	protected function assets() {
		return [
			'css' => ['layouts/full-width','layouts/image-row','issues/listing'],
			'js' => ['layouts/image-row'],
		];
	}

	protected function metaTitle() {
		return 'Browse all our latest issues | Jack Magazine';
	}

	protected function metaDescription() {
		return 'Browse all our latest issues: '.join(', ', F\pluck($this->data['issues'], 'title')).' | Jack Magazine';
	}

	protected function templatePath() {
		return 'issues/index';
	}

	protected function fetchData($args) {
		$issues = new IssuesCollection();
		$issues->fetchAll();
		$this->data['issues'] = $issues;
	}

}
