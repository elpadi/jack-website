<?php
namespace Website\Shop;

use Functional as F;

class Cart {

	protected $items;
	
	public function __construct($storage) {
		$this->storage = $storage;
		$this->items = new CartItemList();
		if ($sessionItems = $this->storage->get('items')) {
			$this->items->hydrateFromSession($sessionItems);
		}
	}

	protected function updateSession() {
		$this->storage->set('items', $this->items->getArrayCopy());
	}

	public function getItems() {
		return $this->items;
	}

	public function getVariants() {
		return F\map($this->items, function($count, $key) {
			return Square::getCatalog()->offsetGet($key);
		});
	}

	public function addItem(string $itemId, string $variantId) {
		if ($this->items->has($itemId, $variantId)) throw new \BadMethodCallException("Cannot add existing item.");
		$this->items->add($itemId, $variantId);
		$this->updateSession();
	}

	public function updateItem(string $itemId, string $variantId, int $newCount) {
		if ($newCount < 0) throw new \RangeException("Negative counts do not make sense.");
		$this->items->update($itemId, $variantId, $newCount);
		$this->updateSession();
	}

}
