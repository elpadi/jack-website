<?php
namespace Website\Issues;

use Functional as F;
use Website\DataCollection;
use Website\DataObject;

class Layouts extends DataCollection {

	use IssueObjectTrait;

	public static function getBySlug(int $issueId, string $slug) {
		return static::getOneIssueObject($issueId, compact('slug'));
	}

	protected static function newItem() {
		return new Layout();
	}

	protected function collectionName() {
		return "issue{$this->issueId}layouts";
	}

}
