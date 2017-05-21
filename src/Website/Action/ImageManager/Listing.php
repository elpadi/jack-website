<?php
namespace Website\Action\ImageManager;

use Website\AssetManager;

class Listing extends Index {

	protected function templatePath() {
		throw new \BadMethodCallException("This method must not be called.");
	}

	protected function fetchData($args) {
		$find = sprintf('find %s \( -name cache -o -name src \) -prune -o -type f -name \'*.jpg\' -print', AssetManager::path());
		$msg = exec($find, $files, $code);
		if ($code !== 0) {
			var_dump(__FILE__.":".__LINE__." - ".__METHOD__, $code, $msg);
			exit(1);
		}
		$this->data = ['images' => array_map(function($filepath) { return str_replace(PUBLIC_ROOT_DIR.'/', '', $filepath); }, $files)];
	}

}
