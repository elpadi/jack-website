<?php
namespace Website\Action\ImageManager;

use Jack\Images\Image;

class Info extends Index {

	protected function templatePath() {
		throw new \BadMethodCallException("This method must not be called.");
	}

	protected function fetchData($args) {
		global $app;
		$path = str_replace('_', '/', $args['path']);
		$cacheKey = md5($path);
		$cacheItem = $app->imageManager->metaCache->getItem($cacheKey);
		if ($cacheItem->isHit()) {
			$image = new Image($path);
			$image->setMeta($cacheItem->get());
			$dims = $image->resizedDims($args['size']);
			$sizeHash = md5(serialize([$image->path, $dims->getWidth(), $dims->getHeight()]));
		}
		$this->data = [
			'meta' => array_merge(['hash' => $cacheKey], $cacheItem->get()),
			'size' => $cacheItem->isHit() ? [
				'hash' => $sizeHash,
				'width' => $dims->getWidth(),
				'height' => $dims->getHeight(),
			] : NULL,
		];
	}

}
