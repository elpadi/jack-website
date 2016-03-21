<?php
namespace Website;

class AssetManager extends \Jack\AssetManager {

	protected static function getAssetsDir() {
		return WEBSITE_DIR.'/assets';
	}

	protected static function getPublicDir() {
		return PUBLIC_ROOT_DIR.'/assets';
	}

	protected static function getPublicPath() {
		return PUBLIC_ROOT.'assets';
	}

}
