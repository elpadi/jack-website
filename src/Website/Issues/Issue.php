<?php
namespace Website\Issues;

use Website\App;
use Website\Shop;

class Issue {

	protected $part = 1;

	public function __construct() {
	}

	public function hydrate($data) {
		foreach ($data as $key => $val) $this->$key = $val;
	}

	public function getResponsiveLayout($layout, $part=1) {
		$path = App::$container['assets']->url(sprintf('issues/%d-%s/part-%d/%s.jpg', $this->number, $this->slug, $part, $layout));
		return [
			'src' => App::$container['images']->imageUrl($path, 'medium'),
			'srcset' => App::$container['images']->responsiveImageSrcset($path, ['medium','large']),
		];
	}

}
