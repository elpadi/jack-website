<?php
namespace Website\Data;

use Functional as F;
use Website\App;
use Website\Shop;

class DataObject {

	final public function __construct() {
	}

	final public function hydrate(array $data) {
		foreach ($data as $key => $val) $this->$key = $val;
	}

	public function getArrayCopy() {
		$data = [];
		foreach ($this as $key => $val) $data[$key] = $val;
		return $data;
	}

	public function getResponsiveLayout($relativePath) {
		$realPath = App::url($relativePath);
		return [
			'src' => App::$container['images']->imageUrl($realPath),
			'srcset' => App::$container['images']->responsiveImageSrcset($realPath),
		];
	}

}
