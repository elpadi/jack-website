<?php
namespace Website\Action\Pages;

use Jack\Action\Page;
use Website\Models;

class Jbpc extends Page {

	protected function assets() {
		return [
			'css' => ['layouts/full-width','pages/home','pages/models'],
			'js' => ['pages/models'],
		];
	}

	protected function fetchPageData() {
		$this->shortcodes->addHandler('jbpc_models', ['\\Website\\Pages', 'modelsShortcode']);
		parent::fetchPageData();
	}

}
