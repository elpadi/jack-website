<?php
namespace Website\Issues;

use Functional as F;
use Website\DataCollection;

class Issues extends DataCollection {

	protected static function newItem() {
		return new Issue();
	}

	protected function collectionName() {
		return 'issues';
	}

	protected function sortEntries(&$entries) {
		array_multisort(F\pluck($entries, 'id'), \SORT_DESC, $entries);
	}

	protected function sort() {
		$this->uasort(function($a, $b) {
			return $b->id - $a->id;
		});
	}

	public function fetchAll() {
		parent::fetchAll();
		$this->sort();
	}

	public function fetchById(int $id) {
		parent::fetchOne(compact('id'));
	}

}
