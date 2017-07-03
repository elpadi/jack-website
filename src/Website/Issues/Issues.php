<?php
namespace Website\Issues;

use Functional as F;
use Website\Data\Collection as DataCollection;

class Issues extends DataCollection {

	protected static function newItem() {
		return new Issue();
	}

	protected function collectionName() {
		return 'issues';
	}

	protected function sort(&$entries) {
		array_multisort(F\pluck($entries, 'id'), \SORT_DESC, $entries);
	}

	public function fetchById(int $id) {
		parent::fetchOne(compact('id'));
	}

}
