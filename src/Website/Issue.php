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

}
