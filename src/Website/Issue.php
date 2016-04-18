<?php
namespace Website;

use Jack\Propel\IssueQuery;

class Issue {

	public static function handleSubmission($data) {
		$issue = empty($data['id']) ? Model::create('issue') : Model::byId('issue', $data['id']);
		$issue->setNumber($data['number']);
		$issue->setTitle($data['title']);
		$issue->setSlug("$data[number]-".s($data['title'])->slugify());
		$issue->setPublished($data['published'] === 'on');
		$issue->save();
	}

	public static function getImages($number, $part, $size=4) {
		$images = array_map(function($path) { return str_replace(AssetManager::getAssetsDir().'/', '', $path); }, glob(AssetManager::getAssetsDir().sprintf('/issue-%d/layouts/part-%d/%d/*.jpg', $number, $part, $size)));
		usort($images, function($a, $b) {
			return static::imageOrderScore(basename($a)) - static::imageOrderScore(basename($b));
		});
		return $images;
	}

	public static function imageOrderScore($name) {
		$score = ctype_digit($name[0]) ? intval(preg_replace('/([0-9]+)[^0-9].*/', '$1', $name)) * 2 : 0;
		if (strpos($name, 'cover') !== false) $score -= 100;
		if (strpos($name, 'front') !== false) $score -= 1;
		return $score;
	}

}
