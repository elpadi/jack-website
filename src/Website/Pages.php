<?php
namespace Website;

use Functional as F;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class Pages extends DataCollection {

	protected function collectionName() {
		return "pages";
	}

	public static function modelsShortcode(ShortcodeInterface $s) {
		global $app;
		$models = new Models();
		return $app->templateManager->snippet('models/list', ['models' => $models->fetchAll()]);
	}

}
