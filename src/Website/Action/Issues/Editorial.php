<?php
namespace Website\Action\Issues;

use Functional as F;

class Editorial extends Issue {

	protected function templatePath() {
		return 'issues/sections';
	}

	protected function assets() {
		return [
			'css' => ['issues/sections'],
			'js' => ['issues/sections'],
		];
	}

	protected function metaTitle() {
		return sprintf('Editorial | Issue #%d - %s | Jack Magazine', $this->data['issue']['number'], $this->data['issue']['title']);
	}

	protected function fetchSections($issue, $part) {
		global $app;
		$sections = cockpit('collections:find', sprintf('sections%dx%d', $issue['number'], $part));
		if (empty($sections)) throw new \InvalidArgumentException("No sections found for part $part of issue $issue[number] - $issue[title].", 404);
		foreach ($sections as &$s) {
			$s['url'] = $app->routeLookUp('section', [
				'slug' => $issue['slug'],
				'part' => $part,
				'section' => $s['slug'],
			]);
			$s['layouts'] = array_map(function($field) {
				global $app;
				$layout = cockpit('collections:findOne', $field['field']['options']['link'], ['_id' => $field['value']['_id']]);
				$layout['src'] = $app->imageManager->imageUrl($app->url($layout['image']['path']), 'medium');
				$layout['srcset'] = $app->imageManager->responsiveImageSrcset($app->url($layout['image']['path']), ['medium','large']);
				return $layout;
			}, $s['layouts']);
		}
		return $sections;
	}

	protected function finalize($response) {
		$this->data['assets'] = array_merge_recursive($this->baseAssets(), $this->assets());
		return parent::finalize($response);
	}

	protected function fetchData($args) {
		$issue = $this->fetchIssue($args['slug']);
		$sections = array_merge($this->fetchSections($issue, 1), $this->fetchSections($issue, 2));
		$this->data = array_merge($args, compact('issue','sections'));
	}

}
