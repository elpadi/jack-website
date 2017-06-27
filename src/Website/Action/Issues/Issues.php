<?php
namespace Website\Action\Issues;

use Functional as F;
use Jack\Action\Page;
use Website\Issues as IssuesModel;

class Issues extends Page {

	protected $title;
	protected $description;

	protected function assets() {
		return [
			'css' => ['sections/sub-nav'],
			'js' => [],
		];
	}

	protected function metaTitle() {
	}

	protected function metaDescription() {
	}

	protected function fetchData($args) {
		$this->data['issues'] = IssuesModel::fetchAll();
	}

}
