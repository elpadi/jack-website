<?php
namespace Website\Action;

class Section extends Issue {

	protected function templatePath() {
		return 'issues/section';
	}

	public function fetchLayout($info) {
		return cockpit('collections:findOne', sprintf('layouts%d', $this->data['issue']['number']), ['_id' => $info['value']['_id']]);
	}

	protected function fetchSection($number, $part, $slug) {
		$section = cockpit('collections:findOne', sprintf('sections%dx%d', $number, $part), compact('slug'));
		$section['layouts'] = array_map([$this, 'fetchLayout'], $section['layouts']);
		return $section;
	}

	protected function metaTitle() {
		return sprintf('%s | Issue #%d | Part %d Editorial | Jack Magazine', $this->data['section']['title'], $this->data['number'], $this->data['part']);
	}

	protected function fetchData($args) {
		parent::fetchData($args);
		$this->data['section'] = $this->fetchSection($this->data['issue']['number'], $args['part'], $args['section']);
	}
}
