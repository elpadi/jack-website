<?php
namespace Website\Shop;

use Functional as F;

class Cart {

	protected static $_metaKeys = ['count','amount'];

	protected function __construct() {
		$session_factory = new \Aura\Session\SessionFactory;
		$session = $session_factory->newInstance($_COOKIE);
		$this->data = $session->getSegment(static::getClass());
		if (!$this->data->get('meta')) $this->initMeta();
	}

	protected function initMeta() {
		$this->data->set('meta', [
			'count' => 0,
			'amount' => 0,
		]);
	}

	protected function getItems(string $id='', string $variant_id='') {
		$items = ($sessionItems = $this->data->get('items')) ? $sessionItems : [];
		if ($variant_id) return isset($items[$id]) && isset($items[$id][$variant_id]) ? $items[$id][$variant_id] : 0;
		if ($id) return isset($items[$id]) ? $items[$id] : [];
		return $items;
	}

	protected function setItem(string $id, string $variant_id, int $count) {
		$items = $this->getItems();
		Square::instance()->validateIds($id, $variant_id);
		if (!isset($items[$id])) $items[$id] = [$variant_id => $count];
		else $items[$id][$variant_id] = $count;
		$this->data->set('items', $items);
	}

	public function itemCount(string $id='', string $variant_id='') {
		$items = $this->getItems($id, $variant_id);
		return is_array($items) ? array_sum(F\flatten($items)) : $items;
	}

	public function totalAmount() {
		$items = $this->getItems();
		return array_sum(F\map($items, function($variants, $id) {
			return array_sum(F\map($variants, function($count, $variant_id) use ($id) {
				return $count * Square::instance()->itemPrice($id, $variant_id);
			}));
		}));
	}

	public function addItem(string $id, string $variant_id) {
		$count = $this->getItems($id, $variant_id);
		if ($count !== 0) throw new \BadMethodCallException("Cannot add existing item.");
		$this->setItem($id, $variant_id, 1);
	}

	public function updateItem(string $id, string $variant_id, int $newCount) {
		if ($newCount < 0) throw new \RangeException("Negative counts do not make sense.");
		$count = $this->getItems($id, $variant_id);
		if ($count === 0) throw new \BadMethodCallException("Cannot update non-existing item.");
		$this->setItem($id, $variant_id, $newCount);
	}

	public static function instance() {
		$class = static::getClass();
		return new $class();
	}

	protected static function getClass() {
		return __CLASS__;
	}

}
