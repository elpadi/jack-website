<?php
namespace Website;

use \Functional as F;

class App extends \Jack\App {

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

}
