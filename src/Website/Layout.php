<?php
namespace Website;

use Jack\Propel\LayoutQuery;
use Jack\Propel\Layout as PropelModel;

class Layout {

	public static function handleSubmission($data) {
		$issue = Model::byId('issue', $data['issue_id']);

		if ($data['section_id'] === '0') {
			$section = Model::create('section');
			$section->setTitle($data['section_title']);
			$section->setSlug(s($data['section_title'])->slugify());
			$section->setIssue($issue);
			$section->save();
		}
		else $section = Model::byId('section', $data['section_id']);

		if (empty($data['id'])) {
			$layout = Model::create('layout');
			$layout->setIssue($issue);
			$layout->setSection($section);
		}
		else $layout = Model::byId('layout', $data['id']);
		
		$layout->setTitle($data['title']);
		$layout->setSlug(s($data['title'])->slugify());
		$layout->setRows($data['rows']);
		$layout->setColumns($data['columns']);
		$layout->save();
	}

}
