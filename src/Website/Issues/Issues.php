<?php
namespace Website\Issues;

use Functional as F;

class Issues extends \ArrayIterator {

	public function __construct() {
		parent::__construct([]);
	}

	protected static function createIssue($data) {
		$issue = new Issue();
		$issue->hydrate($data);
		return $issue;
	}

	protected function sortEntries(&$entries) {
		array_multisort(F\pluck($entries, 'id'), \SORT_DESC, $entries);
	}

	public function fetchAll() {
		$entries = cockpit('collections:find', 'issues');
		$this->sortEntries($entries);
		foreach ($entries as $data) $this->append(static::createIssue($data));
	}

}
