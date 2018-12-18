<?php
namespace Website\Action\Issue;

use Functional as F;
use Website\Pages;

class JackBlackPussyCat extends Issue {

	protected function issueAssets() {
		return [
			'css' => ['layouts/full-width','pages/home','pages/models'],
			'js' => ['pages/models'],
		];
	}

	protected function templatePath() {
		return 'issues/page';
	}

	protected function metaTitle() {
		return 'Overview | '.parent::metaTitle();
	}

	protected function fetchData($args) {
		parent::fetchData($args);
		$page = Pages::getOne(['path' => '/jbpc']);
		$this->shortcodes->addHandler('jbpc_models', ['\\Website\\Pages', 'modelsShortcode']);
		$this->data['content'] = $page->content;
	}

}
