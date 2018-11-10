<?php
namespace Website\Action\Pages;

use Functional as F;
use Jack\Action\Page;

class Intro extends Page {

	protected function fetchIntroImages() {
		$files = glob(PUBLIC_ROOT_DIR.'/assets/intro/*.jpg');
		$this->data['images'] = array_combine(array_map(F\partial_any('basename', F\…, '.jpg'), $files), array_map(function($filepath) {
			global $app;
			$url = str_replace(PUBLIC_ROOT_DIR, '', $filepath);
			return [
				'src' => $app->imageManager->imageUrl($url),
				'srcset' => $app->imageManager->responsiveImageSrcset($url),
			];
		}, $files));
	}

	protected function fetchPageData() {
		parent::fetchPageData();
		$this->fetchIntroImages();
	}

}
