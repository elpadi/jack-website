<?php
namespace Website\Shop;

use Functional as F;
use Website\Shop\Square;
use Website\Shop\SquareCatalog;

class CartItemList extends \ArrayObject {

	const STANDARD_SHIPPING_COST = 35;

	public function __construct() {
		parent::__construct([]);
		$this->catalog = Square::getCatalog();
	}

	public function add($itemId, $variantId) {
		if (!$this->catalog->has($itemId, $variantId)) throw new \InvalidArgumentException("ID tuple ($itemId | $variantId) is not valid.");
		$this->offsetSet(Square::key($itemId, $variantId), 1);
	}

	public function update($itemId, $variantId, $newCount) {
		if (!$this->has($itemId, $variantId)) throw new \BadMethodCallException("Cannot update non-existing item.");
		$this->offsetSet(Square::key($itemId, $variantId), $newCount);
	}

	public function has($itemId, $variantId) {
		$key = Square::key($itemId, $variantId);
		return $this->offsetExists($key) && $this->offsetGet($key) > 0;
	}

	public function getItemCount() {
		return array_sum($this->getArrayCopy());
	}

	public function getSubtotal() {
		return array_sum(F\map($this, function($count, $key) {
			return Square::getCatalog()->offsetGet($key)->getPrice() * $count;
		}));
	}

	public function getShipping() {
		$count = $this->getItemCount();
		return ceil($count / 3) * self::STANDARD_SHIPPING_COST;
	}

	public function getVariantCount($variant) {
		return $this->has($variant->getItemId(), $variant->getVariantId())
			? $this->offsetGet(Square::key($variant->getItemId(), $variant->getVariantId()))
			: 0;
	}

	public function hydrateFromSession($sessionItems) {
		foreach ($sessionItems as $key => $count) $this->offsetSet($key, $count);
	}

}
