<?php
namespace Website\Action;

use Functional as F;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Website\Models;

class Home extends Intro {

	protected function assets() {
		return [
			'css' => ['layouts/full-width','pages/intro','pages/home','pages/models'],
			'js' => ['pages/intro','pages/models'],
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
