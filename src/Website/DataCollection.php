<?php
namespace Website;

use Functional as F;

abstract class DataCollection extends \ArrayIterator {

	public function __construct() {
		parent::__construct([]);
	}

	protected static function newItem() {
		return new DataObject();
	}

	abstract protected function collectionName();

	public static function getAll() {
		$collection = new static();
		$collection->fetchAll();
		return $collection;
	}

	public static function getOne($where) {
		$collection = new static();
		$collection->fetchOne($where);
		return $collection->current();
	}

	public static function createFromChildren(array $children) {
		$collection = new static();
		foreach ($children as $child) {
			$data = cockpit('collections:findOne', $child['field']['options']['link'], ['_id' => $child['value']['_id']]);
			$collection->append(static::createItem($data));
		}
		return $collection;
	}

	protected static function createItem(array $data) {
		$item = static::newItem();
		if (!($item instanceof DataObject)) throw new \RuntimeException("Item created must be an instance of DataObject.");
		$item->hydrate($data);
		return $item;
	}

	public function fetchAll() {
		$entries = cockpit('collections:find', $this->collectionName());
		foreach ($entries as $data) $this->append(static::createItem($data));
	}

	public function fetchOne(array $where) {
		if ($data = cockpit('collections:findOne', $this->collectionName(), $where)) {
			$this->append(static::createItem($data));
		}
	}

}
