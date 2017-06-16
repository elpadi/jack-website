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
		global $app;
		$this->data['homepage'] = cockpit('collections:findOne', 'pages', ['path' => $app->routeLookup(getenv('HOME_CONTENT_PAGE'))]);
		$this->shortcodes->addHandler('jbpc_models', [$this, 'modelsShortcode']);
		parent::fetchPageData();
		$this->data['content'] = $this->data['homepage']['content'];
		unset($this->data['homepage']['content']);
	}

}
