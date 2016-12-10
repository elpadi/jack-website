<?php
namespace Website\Action;

class Editorial extends Issue {

	protected function templatePath() {
		return 'issues/editorial';
	}

	protected function metaTitle() {
		return sprintf('%s | Issue #%d | Part %d Editorial | Jack Magazine', $this->data['issue']['title'], $this->data['number'], $this->data['part']);
	}

	protected function fetchData($args) {
		parent::fetchData($args);
		$this->data['sections'] = $this->issuePart($this->data['issue'], $args['part']);
	}
}
