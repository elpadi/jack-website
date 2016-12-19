<?php
namespace Website\Action;

use Functional as F;

class Section extends Issue {

	protected function templatePath() {
		return 'issues/section';
	}

	public function fetchLayout($info) {
		$layout = cockpit('collections:findOne', sprintf('layouts%d', $this->data['issue']['number']), ['_id' => $info['value']['_id']]);
		$layout['image']['src'] = \Jack\Images::resizeImage(\Jack\App::instance()->url($layout['image']['path']), 'small');
		return $layout;
	}

	protected function fetchSection($number, $slug) {
		return F\head(F\select(F\map([1,2], function($part) use ($number, $slug) {
			return cockpit('collections:findOne', sprintf('sections%dx%d', $number, $part), compact('slug'));
		}), 'Functional\id'));
	}

	protected function metaTitle() {
		return sprintf('%s | Issue #%d | Part %d Editorial | Jack Magazine', $this->data['section']['title'], $this->data['number'], $this->data['part']);
	}

	protected function fetchData($args) {
		if (($issue = $this->fetchIssue($args['slug'])) && ($section = $this->fetchSection($issue['number'], $args['section']))) {
			$this->data = array_merge($args, compact('issue','section'));
			$this->data['layouts'] = F\map($this->data['section']['layouts'], [$this, 'fetchLayout']);
			unset($this->data['section']['layouts']);
		}
	}
}
