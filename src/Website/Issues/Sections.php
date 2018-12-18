<?php
namespace Website\Issues;

use Functional as F;
use Website\Data\DynamicNameCollection;

class Sections extends DynamicNameCollection {

	protected static function getNameSetter(int $issueId) {
		return function(&$collection) use ($issueId) { $collection->NAME = "issue{$issueId}sections"; };
	}

	protected static function newItem() {
		return new Section();
	}

	public static function getByIssueId(int $issueId) {
		return static::getAll(static::getNameSetter($issueId));
	}

}
