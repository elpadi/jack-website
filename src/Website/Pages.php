<?php
namespace Website;

use Functional as F;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Website\Data\StaticNameCollection;
use Website\Models\Models;

class Pages extends StaticNameCollection {

	protected static $NAME = 'pages';

	public static function modelsShortcode(ShortcodeInterface $s) {
		return App::$container['templates']->snippet('models/list', ['models' => Models::getAll()]);
	}

	public static function getHomePage() {
		return static::getOne(['path' => App::routeUrl(getenv('HOME_CONTENT_PAGE'))]);
	}

}
