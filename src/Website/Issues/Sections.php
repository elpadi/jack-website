<?php
namespace Website\Issues;

use Functional as F;
use Website\DataCollection;

class Sections extends DataCollection {

	use IssueObjectTrait;

	public static function getByIssueId(int $issueId) {
		return static::getAllIssueObjects($issueId);
	}

	protected static function newItem() {
		return new Section();
	}

	protected function collectionName() {
		return "issue{$this->issueId}sections";
	}

}
