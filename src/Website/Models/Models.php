<?php
namespace Website\Models;

use function Stringy\create as s;
use Website\Data\Collection as DataCollection;

class Models extends DataCollection {

	protected function collectionName() {
		return 'models';
	}

	public static function getOneRandomWithout($excludes) {
		$collection = new static();
		$collection->filter(function($data) use ($excludes) {
			return strpos($excludes, (string)s($data['name'])->slugify()) === FALSE;
		});
		$collection->randomize();
		$collection->fetchAll();
		return $collection->current();
	}

}
