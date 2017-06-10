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

	public function itemCount($id='', $variant_id='') {
		$items = $this->data->get('items');
		if ($variant_id) return isset($items[$id]) && isset($items[$id][$variant_id]) ? $items[$id][$variant_id] : 0;
		if ($id) return isset($items[$id]) ? array_sum($items[$id]) : 0;
		return $items ? array_sum(array_map(function($variants) {
			return array_sum($variants);
		}, $items)) : 0;
	}

	public function totalAmount() {
		$items = $this->data->get('items');
		return $items ? array_sum(F\map($items, function($variants, $id) {
			return array_sum(F\map($variants, function($count, $variant_id) use ($id) {
				return $count * Square::instance()->itemPrice($id, $variant_id);
			}));
		})) : 0;
	}

	public function addItem($id, $variant_id) {
		$items = $this->data->get('items');
		if ($items && isset($items[$id]) && isset($items[$id][$variant_id])) $items[$id][$variant_id]++;
		else $items[$id] = [$variant_id => 1];
		$this->data->set('items', $items);
		return $items[$id][$variant_id];
	}

	public static function instance() {
		$class = static::getClass();
		return new $class();
	}

	protected static function getClass() {
		return __CLASS__;
	}

}
