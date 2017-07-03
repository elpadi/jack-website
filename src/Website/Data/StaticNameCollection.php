<?php
namespace Website\Data;

abstract class StaticNameCollection extends Collection {

	protected static $NAME;

	protected function collectionName() {
		if (!static::$NAME) throw new \BadMethodCallException("Subclass must define the collection name.");
		return static::$NAME;
	}

	protected static function one(callable $fn) {
		$collection = new static();
		call_user_func_array($fn, [&$collection]);
		return $collection->current();
	}

	protected static function many(callable $fn) {
		$collection = new static();
		call_user_func_array($fn, [&$collection]);
		return $collection;
	}

	public static function getAll() {
		return static::many(function(&$collection) { $collection->fetchAll(); });
	}

	public static function getOne($where) {
		return static::one(function(&$collection) use ($where) { $collection->fetchOne($where); });
	}

	public static function getOneRandom() {
		return static::one(function(&$collection) {
			$collection->randomize();
			$collection->fetchAll();
		});
	}

	public static function createFromChildren(array $children) {
		return static::many(function(&$collection) use ($children) {
			foreach ($children as $child) $collection->append(static::createItem(static::getChildData($child)));
		});
	}

}
