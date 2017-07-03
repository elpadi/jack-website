<?php
namespace Website\Issues;

use Functional as F;
use Website\Data\StaticNameCollection;

class Issues extends StaticNameCollection {

	protected static $NAME = 'issues';

	protected static function newItem() {
		return new Issue();
	}

	protected function sort(&$entries) {
		array_multisort(F\pluck($entries, 'id'), \SORT_DESC, $entries);
	}

	public function fetchById(int $id) {
		parent::fetchOne(compact('id'));
	}

}
