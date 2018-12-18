<?php
namespace Website\Data;

use Functional as F;
use Website\Data\Object as DataObject;

abstract class Collection extends \ArrayIterator {

	protected $sortingFunction;
	protected $filters = array();

	public function __construct(array $values=[]) {
		parent::__construct($values);
	}

	protected static function newItem() {
		return new DataObject();
	}

	abstract protected function collectionName();

	protected static function createItem(array $data) {
		$item = static::newItem();
		if (!($item instanceof DataObject)) throw new \RuntimeException("Item created must be an instance of Website\\Data\\Object.");
		$item->hydrate($data);
		return $item;
	}

	protected static function getChildData($child) {
		return cockpit('collections:findOne', $child['field']['options']['link'], ['_id' => $child['value']['_id']]);
	}

	protected function randomize() {
		$this->enableSort(function(&$entries) { shuffle($entries); });
	}

	protected function select($entries) {
		if (!empty($this->filters)) foreach ($this->filters as $f) $entries = F\select($entries, $f);
		return $entries;
	}

	public function filter(callable $fn) {
		array_push($this->filters, $fn);
	}

	protected function sort(&$entries) {
		if (is_callable($this->sortingFunction)) call_user_func_array($this->sortingFunction, [&$entries]);
	}

	public function enableSort(callable $fn) {
		$this->sortingFunction = $fn;
	}

	public function fetchAll() {
		$entries = cockpit('collections:find', $this->collectionName());
		if ($entries === FALSE) throw new \RuntimeException(sprintf('Collection "%s" does not exist.', $this->collectionName()));
		$entries = $this->select($entries);
		$this->sort($entries);
		foreach ($entries as $data) $this->append(static::createItem($data));
	}

	public function fetchOne(array $where) {
		if ($data = cockpit('collections:findOne', $this->collectionName(), $where)) {
			$this->append(static::createItem($data));
		}
	}

}
