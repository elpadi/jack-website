<?php
namespace Website\Action\Issue;

use Functional as F;

class Layouts extends Issue {

	protected function templatePath() {
		return 'issues/grid';
	}

	protected function issueAssets() {
		return [
			'css' => ['layouts/full-width','layouts/image-grid','issues/image-grid'],
			'js' => ['layouts/image-grid','issues/image-grid'],
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
