<?php
namespace Website;

use Jack\Propel\IssueQuery;
use Jack\Propel\Issue;
use Jack\Propel\SectionQuery;
use Jack\Propel\Section;
use Jack\Propel\LayoutQuery;
use Jack\Propel\Layout;
use Jack\Propel\PosterQuery;
use Jack\Propel\Poster;

class Model {

	protected static function createQuery($type) {
		switch ($type) {
			case 'issue': return IssueQuery::create();
			case 'section': return SectionQuery::create();
			case 'layout': return LayoutQuery::create();
			case 'poster': return PosterQuery::create();
		}
		throw new \InvalidArgumentException("Model type '$type' is not recognized.");
	}

	protected static function fetch($type, $method) {
		$query = static::createQuery($type);
		return call_user_func_array([$query, $method], array_slice(func_get_args(), 2));
	}

	public static function create($type) {
		switch ($type) {
			case 'issue': return new Issue();
			case 'section': return new Section();
			case 'layout': return new Layout();
			case 'poster': return new Poster();
		}
		throw new \InvalidArgumentException("Model type '$type' is not recognized.");
	}

	public static function bySlug($type, $slug) {
		return static::fetch($type, 'requireOneBySlug', $slug);
	}

	public static function byId($type, $id) {
		return static::fetch($type, 'requireOneById', $id);
	}

	public static function byIssue($type, $id) {
		return static::fetch($type, 'findByIssueId', $id);
	}

	public static function all($type) {
		return static::fetch($type, 'find');
	}

}
