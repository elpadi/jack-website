<?php
namespace Website\Action\Issue;

use Functional as F;

class Layouts extends Issue {

	protected function templatePath() {
		return 'issues/layouts';
	}

	protected function issueAssets() {
		return [
			'css' => ['layouts/full-width','issues/layouts'],
			'js' => [],
		];
	}

	protected function metaTitle() {
		return 'Layouts | '.parent::metaTitle();
	}

	protected function api($response) {
		$this->data['layouts'] = F\flatten(F\invoke($this->data['issue']->getSections(), 'getLayouts'));
		return parent::api($response);
	}

}
