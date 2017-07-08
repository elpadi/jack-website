<?php
namespace Website\Issues;

use Functional as F;
use Website\App;
use Website\Data\Object as DataObject;
use Website\Shop;

class Issue extends DataObject {

	public function getStoreVariant() {
		$variant = App::$container['catalog']->getByTitle($this->title);
		if ($variant) {
			$variant->setCartCount(App::$container['cart']->getVariantCount($variant->getItemId(), $variant->getVariantId()));
		}
		return $variant;
	}

	public function getSections() {
		return Sections::getByIssueId($this->id);
	}

	protected function getRouteUrl($name, $args=[]) {
		return App::routeUrl($name, array_merge(['id' => $this->id, 'slug' => $this->slug], $args));
	}

	public function getUrl() {
		return $this->getRouteUrl('issue');
	}

	public function getLayoutsUrl() {
		return $this->getRouteUrl('layouts');
	}

	public function getModelsUrl() {
		return $this->getRouteUrl('pussycats');
	}

	public function getResponsiveCovers() {
		return array_map([$this, 'getResponsiveLayout'], F\pluck(array_slice($this->covers, 0, 1), 'path'));
	}

}
