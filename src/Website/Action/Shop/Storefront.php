<?php
namespace Website\Action\Shop;

use Jack\Action\Page;
use Website\Issues\Issue;
use Website\Shop\Cart;

class Storefront extends Page {

	protected function templatePath() {
		return 'store/front';
	}

	protected function assets() {
		return [
			'css' => ['store/front'],
			'js' => ['store/front'],
		];
	}

	protected function sectionTitle() {
		return 'Get the latest issue';
	}

	protected function metaTitle() {
		return sprintf('%s | Jack Magazine Shop', $this->sectionTitle());
	}

	protected function issueToItem($number, $part) {
		$issue = new Issue($number, $part);
		$issue->fetchResponsiveLayout('cover_front');
		$issue->fetchShopData();
		$issue->cartCount = Cart::instance()->itemCount($issue->shopData['formatted']['id'], $issue->shopData['formatted']['variant_id']);
		return $issue;
	}

	protected function fetchData($args) {
		$this->data['cartItemCount'] = Cart::instance()->itemCount();
		foreach (range(1,2) as $part) $this->data['issues'][] = $this->issueToItem(1, $part);
	}

}

