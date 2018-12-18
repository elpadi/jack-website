<?php
namespace Website\Shop;

use Functional as F;

class Catalog extends Square\Catalog {

	public function __construct() {
		parent::__construct([]);
		$this->hydrate();
	}

	protected function fetchProducts() {
		foreach ($this->raw as $item) {
			if ($item->type !== 'ITEM' || $item->item_data->product_type !== 'REGULAR') continue;
			foreach ($item->item_data->variations as $variant) {
				if ($variant->type !== 'ITEM_VARIATION') continue;
				$this->offsetSet(Square\Store::key($item->id, $variant->id), new Variant($variant, $item));
			}
		}
	}

	protected function fetchDiscounts() {
		foreach (explode(',', $_ENV['DISCOUNTS']) as $_d) {
			extract(array_combine(['code','amount'], explode(':', $_d)));
			$this->offsetSet($code, intval($amount));
		}
	}

	protected function hydrate() {
		$this->fetchProducts();
		$this->fetchDiscounts();
	}

	public function getByTitle($s) {
		foreach ($this as $variant) if ($variant->getItemTitle() === $s) return $variant;
		//trigger_error("Variant with title '$s' does not exist.", \E_USER_WARNING);
	}

}
