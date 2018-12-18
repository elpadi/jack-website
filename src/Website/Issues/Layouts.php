<?php
namespace Website\Issues;

use Functional as F;
use Website\Data\DynamicNameCollection;

class Layouts extends DynamicNameCollection {

	protected static function getNameSetter(int $issueId) {
		return function(&$collection) use ($issueId) { $collection->NAME = "issue{$issueId}layouts"; };
	}

	protected static function newItem() {
		return new Layout();
	}

	public static function getById(int $issueId, string $id) {
		return static::getOne(['_id' => $id], static::getNameSetter($issueId));
	}

	public static function getBySlug(int $issueId, string $slug) {
		return static::getOne(compact('slug'), static::getNameSetter($issueId));
	}

	public static function getByIssueId(int $issueId) {
		return static::getAll(static::getNameSetter($issueId));
	}

}
