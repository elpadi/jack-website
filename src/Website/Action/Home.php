<?php
namespace Website\Action;

use Functional as F;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Jack\Action\Page;

class Home extends Intro {

	protected function assets() {
		return array_merge_recursive(parent::assets(), [
			'css' => ['pages/intro'],
			'js' => ['pages/intro'],
		]);
	}

	public function modelsShortcode(ShortcodeInterface $s) {
		global $app;
		$models = cockpit('collections:find', 'models');
		return $app->templateManager->snippet('jbpc_models', compact('models'));
	}

	protected function fetchPageData() {
		$this->shortcodes->addHandler('jbpc_models', [$this, 'modelsShortcode']);
		parent::fetchPageData();
	}

}
