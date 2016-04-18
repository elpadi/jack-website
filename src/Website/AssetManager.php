<?php
namespace Website;

class AssetManager extends \Jack\AssetManager {

	public static function getAssetsDir() {
		return PUBLIC_ROOT_DIR.'/admin/assets';
	}

	protected static function getPublicDir() {
		return PUBLIC_ROOT_DIR.'/assets';
	}

	protected static function getPublicPath() {
		return PUBLIC_ROOT.'assets';
	}

}
