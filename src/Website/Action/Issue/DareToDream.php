<?php
namespace Website\Action\Issue;

use Functional as F;

class DareToDream extends Issue {

	protected function issueAssets() {
		return [
			'css' => ['issues/sections'],
			'js' => ['issues/sections'],
		];
	}

	protected function templatePath() {
		return 'issues/sections';
	}

}
