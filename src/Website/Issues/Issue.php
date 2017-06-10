<?php
namespace Website\Issues;

use Website\Shop;

class Issue {

	public $number;
	public $part;

	public function __construct(int $number, int $part) {
		$this->number = $number;
		$this->fetchIssueData();
		$this->part = $part;
	}

	public function fetchIssueData() {
		$data = cockpit('collections:findOne', 'issues', ['number' => $this->number]);
		foreach ($data as $key => $val) $this->$key = $val;
	}

	public function fetchResponsiveLayout($layout) {
		global $app;
		$path = $app->assetUrl(sprintf('issues/%d-%s/part-%d/%s.jpg', $this->number, $this->slug, $this->part, $layout));
		$this->responsiveLayouts[$layout] = [
			'src' => $app->imageManager->imageUrl($path, 'medium'),
			'srcset' => $app->imageManager->responsiveImageSrcset($path, ['medium','large']),
		];
	}

	public function fetchCover() {
		$this->cover = $this->getResponsiveLayout('cover_front');
	}

	public function fetchShopData() {
		$data = Shop\Square::instance()->getIssueItem($this->number, $this->part);
		$variant = $data->item_data->variations[0];
		$this->shopData = [
			'raw' => $data,
			'formatted' => [
				'title' => '',
				'id' => $data->id,
				'variant_id' => $variant->id,
				'price' => number_format($variant->item_variation_data->price_money->amount / 100, 0),
			]
		];
	}

}
