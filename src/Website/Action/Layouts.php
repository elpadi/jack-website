<?php
namespace Website\Action;

class Layouts extends Issue {

	protected function templatePath() {
		return 'issues/grid';
	}

	protected function assets() {
		return [
			'css' => ['layouts/image-grid','issues/image-grid'],
			'js' => ['layouts/image-grid','issues/image-grid'],
		];
	}

	public function fetchLayouts() {
		$part = intval($this->data['part']);
		$layouts = cockpit('collections:find', sprintf('layouts%d', $this->data['issue']['number']));
		$layouts = array_slice($layouts, ($part - 1) * count($layouts) / 2, round(count($layouts) / 2) - 1);
		foreach ($layouts as &$layout) {
			$layout['image']['small'] = \Jack\App::instance()->imageManager->imageUrl(\Jack\App::instance()->url($layout['image']['path']), 'small');
			$layout['image']['medium'] = \Jack\App::instance()->imageManager->imageUrl(\Jack\App::instance()->url($layout['image']['path']), 'medium');
		}
		return $layouts;
	}

	protected function metaTitle() {
		return sprintf('Part %d Layouts | Issue #%d - %s | Jack Magazine', $this->data['part'], $this->data['issue']['number'], $this->data['issue']['title']);
	}

	protected function fetchData($args) {
		if (($issue = $this->fetchIssue($args['slug']))) {
			$this->data = array_merge($args, compact('issue'));
			$this->data['layouts'] = $this->fetchLayouts();
		}
	}

}
