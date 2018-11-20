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
		if ($items = $this->storage->get('items')) {
			foreach ($items as $key => $count) $this->offsetSet($key, $count);
		}
		else {
			if (IS_LOCAL) {
				foreach ($this->catalog as $id => $variant) if (is_object($variant)) $this->offsetSet($id, 1);
				$this->updateStorage();
				return NULL;
			}
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
		$items = ['products' => [], 'discount' => 0];
		foreach ($this as $key => $count) {
			if ($count == 0) continue;
			$item = $this->catalog[$key];
			if ($item instanceof Variant) {
				$item->setCartCount($count);
				$items['products'][] = $item;
			}
			else {
				$items['discount'] += $item;
			}
		}
		return $items;
	}

	protected function has(string $key) {
		return $this->offsetExists($key) && $this->offsetGet($key) > 0;
	}

	public function removeDiscount() {
		foreach ($this->getArrayCopy() as $key => $count)
			if ($this->catalog[$key] instanceof Variant == false)
				$this->offsetUnset($key);
		$this->updateStorage();
	}

	public function discount(string $code) {
		if (!$this->catalog->offsetExists($code)) throw new \InvalidArgumentException("Discount code $code is not valid.");
		if ($this->has($code)) throw new \BadMethodCallException("Discount code already applied.");
		$this->offsetSet($code, $this->catalog[$code]);
		$this->updateStorage();
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
		$items = $this->getItems();
		return array_sum($this->getArrayCopy()) - $items['discount'];
	}

	public function getSubtotal() {
		extract($this->getItems());
		$total = array_sum(F\invoke($products, 'getCartSubtotal'));
		return ['gross' => $total, 'net' => $total - ceil($total * $discount / 100)];
	}

	public function getShipping() {
		return static::$STANDARD_SHIPPING_COST;
	}

	public function clear() {
		$this->storage->clear();
		$this->exchangeArray([]);
	}

}
