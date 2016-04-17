<?php
namespace Website;

use \Functional as F;

class App extends \Jack\App {

	public static function prefix($s) {
		return "jack_website__$s";
	}

	public static function createTemplate() {
		return new Template();
	}	

	public static function createAssetManager() {
		return new AssetManager();
	}	

	public static function url($path) {
		return DEBUG ? PUBLIC_ROOT.$path : static::asset_url($path);
	}

	public static function asset_url($path) {
		return static::$assets->url($path);
	}

	public static function imageUrl($object) {
		switch (end(explode('\\', get_class($object)))) {
			case 'Poster':
				return static::$assets->url(sprintf('issue-%d/posters/%s-%s_%dx%d.jpg',
					$object->getLayout()->getIssue()->getNumber(),
					$object->getPage(),
					strtolower($object->getFace()),
					$object->getRow(),
					$object->getCol()
				));
			case 'Layout': 
				$pages = array_filter(F\invoke($object->getPosters(), 'getPage'));
				return static::$assets->url(sprintf('issue-%d/layouts/%s.jpg',
					$object->getIssue()->getNumber(),
					(count($pages) ? implode('-', $pages).'_' : '').str_replace('-','_',$object->getSlug())
				));
		}
		return '';
	}

	public static function setIntroAsSeen() {
		if (!static::hasSeenIntro()) setcookie(static::prefix('has_seen_intro'), '1', PHP_INT_MAX, PUBLIC_ROOT);
	}

	public static function hasSeenIntro() {
		return isset($_COOKIE[static::prefix('has_seen_intro')]) && $_COOKIE[static::prefix('has_seen_intro')] === '1';
	}

}
