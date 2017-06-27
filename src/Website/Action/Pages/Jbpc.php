<?php
namespace Website\Action\Pages;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Jack\Action\Page;
use Website\Models;

class Jbpc extends Page {

	protected function assets() {
		return [
			'css' => ['layouts/full-width','pages/home','pages/models'],
			'js' => ['pages/models'],
		];
	}

	public function modelsShortcode(ShortcodeInterface $s) {
		global $app;
		$models = new Models();
		return $app->templateManager->snippet('models/list', ['models' => $models->fetchAll()]);
	}

	protected function fetchPageData() {
		$this->shortcodes->addHandler('jbpc_models', [$this, 'modelsShortcode']);
		parent::fetchPageData();
	}

}
