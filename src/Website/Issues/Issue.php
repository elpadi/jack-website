<?php
namespace Website\Issues;

use Functional as F;
use Website\App;
use Website\Shop;

class Issue {

	protected $part = 1;

	public function __construct() {
	}

	public function hydrate($data) {
		foreach ($data as $key => $val) $this->$key = $val;
	}

	public function getUrl() {
		return App::routeUrl('issue', ['id' => $this->id, 'slug' => $this->slug]);
	}

	public function getResponsiveCovers() {
		return array_map([$this, 'getResponsiveLayout'], F\pluck(array_slice($this->covers, 0, 1), 'path'));
	}

	public function getResponsiveLayout($relativePath) {
		$realPath = App::url($relativePath);
		return [
			'src' => App::$container['images']->imageUrl($realPath, 'medium'),
			'srcset' => App::$container['images']->responsiveImageSrcset($realPath, ['medium','large']),
		];
	}

}
