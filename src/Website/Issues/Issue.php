<?php
namespace Website\Issues;

use Website\Shop;

class Issue {

	public static function createByNumber(int $number, int $part=1) {
		$issue = new Issue();
		$issue->hydrate($number);
		$issue->part = $part;
		return $issue;
	}

	protected function __construct() {
	}

	protected function hydrate($number) {
		$data = cockpit('collections:findOne', 'issues', ['number' => $number]);
		foreach ($data as $key => $val) $this->$key = $val;
	}

	public function getResponsiveLayout($layout) {
		global $app;
		$path = $app->assetUrl(sprintf('issues/%d-%s/part-%d/%s.jpg', $this->number, $this->slug, $this->part, $layout));
		return [
			'src' => $app->imageManager->imageUrl($path, 'medium'),
			'srcset' => $app->imageManager->responsiveImageSrcset($path, ['medium','large']),
		];
	}

}
