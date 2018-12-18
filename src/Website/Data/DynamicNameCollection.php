<?php
namespace Website\Data;

abstract class DynamicNameCollection extends Collection {

	protected $NAME;
	protected $sortingFunction;
	protected $filters = array();

	protected function collectionName() {
		if (!$this->NAME) throw new \BadMethodCallException("Subclass must define the collection name.");
		return $this->NAME;
	}

	protected static function one(callable $fn, callable $setName) {
		$collection = new static();
		call_user_func_array($setName, [&$collection]);
		call_user_func_array($fn, [&$collection]);
		return $collection->current();
	}

	protected static function many(callable $fn, callable $setName) {
		$collection = new static();
		call_user_func_array($setName, [&$collection]);
		call_user_func_array($fn, [&$collection]);
		return $collection;
	}

	public static function getAll(callable $setName) {
		return static::many(function(&$collection) { $collection->fetchAll(); }, $setName);
	}

	public static function getOne($where, callable $setName) {
		return static::one(function(&$collection) use ($where) { $collection->fetchOne($where); }, $setName);
	}

	public static function getOneRandom(callable $setName) {
		return static::one(function(&$collection) {
			$collection->randomize();
			$collection->fetchAll();
		}, $setName);
	}

	public static function createFromChildren(array $children) {
		if (empty($children)) throw new \BadMethodCallException("Cannot create collection from empty children list.");
		$name = $children[0]['field']['options']['link'];
		return static::many(function(&$collection) use ($children) {
			foreach ($children as $child) $collection->append(static::createItem(static::getChildData($child)));
		}, function(&$collection) use ($name) { $collection->NAME = $name; });
	}

}
