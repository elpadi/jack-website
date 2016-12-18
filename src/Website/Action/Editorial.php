<?php
namespace Website\Action;

class Editorial extends Issue {

	protected function templatePath() {
		return 'issues/editorial';
	}

	protected function fetchSections($number, $part) {
		global $app;
		$sections = cockpit('collections:find', sprintf('sections%dx%d', $number, $part));
		foreach ($sections as &$s) $s['url'] = $app->routeLookUp('section', [
			'slug' => $this->data['slug'],
			'part' => $this->data['part'],
			'section' => $s['slug'],
		]);
		return $sections;
	}

	protected function metaTitle() {
		return sprintf('Part %d Editorial | Issue #%d %s | Jack Magazine', $this->data['part'], $this->data['number'], $this->data['issue']['title']);
	}

	protected function fetchData($args) {
		parent::fetchData($args);
		$this->data['sections'] = $this->fetchSections($this->data['issue']['number'], $args['part']);
	}
}
