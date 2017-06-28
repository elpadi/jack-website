<?php
namespace Website\Issues;

use Functional as F;

class Issues extends \ArrayIterator {

	public function __construct() {
		parent::__construct([]);
	}

	public function fetchAll() {
		foreach (cockpit('collections:find', 'issues') as $data) {
			$issue = new Issue();
			$issue->hydrate($data);
			$this->append($issue);
		}
	}

}
