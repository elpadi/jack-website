<?php
namespace Website\Shop;

use Functional as F;

class SquareCatalog extends \ArrayObject {

	public function __construct() {
		parent::__construct([]);
	}

	public function has($itemId, $variantId) {
		return $this->offsetExists(Square::key($itemId, $variantId));
	}

	public function getVariant($itemId, $variantId) {
		return $this->has($itemId, $variantId)
			? $this->offsetGet(Square::key($itemId, $variantId))
			: NULL;
	}

	public function hydrateFromApi($response) {
		foreach ($response as $item) {
			if ($item->type !== 'ITEM' || $item->item_data->product_type !== 'REGULAR') continue;
			foreach ($item->item_data->variations as $variant) {
				if ($variant->type !== 'ITEM_VARIATION') continue;
				$this->offsetSet(Square::key($item->id, $variant->id), new SquareVariant($variant, $item));
			}
		}
	}

}
