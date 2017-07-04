<?php
namespace Website\Models;

use function Stringy\create as s;
use Website\Data\StaticNameCollection;

class Models extends StaticNameCollection {

	protected static $NAME = 'models';

	public static function getOneRandomWithout($excludes) {
		return static::one(function(&$collection) use ($excludes) {
			$collection->filter(function($data) use ($excludes) {
				return strpos($excludes, (string)s($data['name'])->slugify()) === FALSE;
			});
			$collection->randomize();
			$collection->fetchAll();
		});
	}

}
