<?php
namespace Website\Action\Shop;

use Jack\Action\Page;
use Website\Issues\Issue;
use Website\Shop\Cart;
use Website\Shop\Square;

class Storefront extends Page {

	protected function templatePath() {
		return 'store/front';
	}

	protected function assets() {
		return [
			'css' => ['store/base','store/front'],
			'js' => ['store/store'],
		];
	}

	protected function sectionTitle() {
		return 'Get the latest issue';
	}

	protected function metaTitle() {
		return sprintf('%s | Jack Magazine Shop', $this->sectionTitle());
	}

}

