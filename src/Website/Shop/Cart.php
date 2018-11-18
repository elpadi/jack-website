<?php
namespace Website\Shop;

use Functional as F;
use Website\Shop\Square\Store;

class Cart extends \ArrayObject {

	protected static $STANDARD_SHIPPING_COST;

	protected $items;
	
	public function __construct($storage, $catalog) {
		parent::__construct([]);
		$this->storage = $storage;
		$this->catalog = $catalog;
		if (!static::$STANDARD_SHIPPING_COST) {
			static::$STANDARD_SHIPPING_COST = intval(getenv('SQUARE_SHIPPING_COST'));
		}
		$this->hydrateFromStorage();
	}

	protected function hydrateFromStorage() {
		if (IS_LOCAL && $this->count() == 0) {
			foreach ($this->catalog as $id => $variant) $this->offsetSet($id, 1);
			$this->updateStorage();
			return NULL;
		}
		if ($items = $this->storage->get('items')) {
			foreach ($items as $key => $count) $this->offsetSet($key, $count);
		}
	}

	public function whenLastUpdated() {
		return ($time = $this->storage->get('lastUpdateTimestamp')) ? $time : 0;
	}

	protected function updateStorage() {
		$this->storage->set('items', $this->getArrayCopy());
		$this->storage->set('lastUpdateTimestamp', time());
	}

	public function getItems() {
		$items = [];
		foreach ($this as $key => $count) {
			$item = $this->catalog[$key];
			$item->setCartCount($count);
			$items[] = $item;
		}
		return $items;
	}

	protected function has(string $key) {
		return $this->offsetExists($key) && $this->offsetGet($key) > 0;
	}

	public function addItem(string $itemId, string $variantId) {
		$key = Store::key($itemId, $variantId);
		if (!$this->catalog->offsetExists($key)) throw new \InvalidArgumentException("ID tuple ($itemId | $variantId) is not valid.");
		if ($this->has($key)) throw new \BadMethodCallException("Cannot add existing item.");
		$this->offsetSet($key, 1);
		$this->updateStorage();
	}

	public function updateItem(string $itemId, string $variantId, int $newCount) {
		$key = Store::key($itemId, $variantId);
		if ($newCount < 0) throw new \RangeException("Negative counts do not make sense.");
		if (!$this->has($key)) throw new \BadMethodCallException("Cannot update non-existing item.");
		$this->offsetSet($key, $newCount);
		$this->updateStorage();
	}

	public function getVariantCount(string $itemId, string $variantId) {
		$key = Store::key($itemId, $variantId);
		return $this->offsetExists($key) ? $this->offsetGet($key) : 0;
	}

	public function getItemCount() {
		return array_sum($this->getArrayCopy());
	}

	public function getSubtotal() {
		return array_sum(F\invoke($this->getItems(), 'getCartSubtotal'));
	}

	public function getShipping() {
		if (DEBUG) return 1;
		/*
		// Calculate shipping cost based on item count.
		$count = $this->getItemCount();
		return ceil($count / 3) * self::$STANDARD_SHIPPING_COST;
		 */
		return static::$STANDARD_SHIPPING_COST;
	}

	public function clear() {
		$this->storage->clear();
		$this->exchangeArray([]);
	}

}
