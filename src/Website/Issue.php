<?php
namespace Website;

use Jack\Propel\IssueQuery;

class Issue extends \Jack\Propel\Issue {

	protected static function fetch($method) {
		$query = IssueQuery::create();
		return call_user_func_array([$query, $method], array_slice(func_get_args(), 1));
	}

	public static function bySlug($slug) {
		return static::fetch('requireOneBySlug', $slug);
	}

	public static function byId($id) {
		return static::fetch('requireOneById', $id);
	}

	public static function all() {
		return static::fetch('find');
	}

	public static function handleSubmission($data) {
		$issue = empty($data['id']) ? new Issue() : static::byId($data['id']);
		$issue->setNumber($data['number']);
		$issue->setTitle($data['title']);
		$issue->setSlug("$data[number]-".s($data['title'])->slugify());
		$issue->setPublished($data['published'] === 'on');
		$issue->save();
	}

}
