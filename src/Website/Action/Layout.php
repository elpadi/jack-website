<?php
namespace Website\Action;

use Functional as F;

class Layout extends Issue {

	protected function templatePath() {
		return 'issues/layout';
	}

	protected function assets() {
		return [
			'css' => ['issues/layout'],
			'js' => ['issues/layout'],
		];
	}

	protected function ogTags() {
		return array_merge(parent::ogTags(), ['image' => $this->canonicalUrl($this->data['layout']['src'])]);
	}

	protected function metaTitle() {
		return sprintf('%s | %s | Issue #%d - %s | Jack Magazine', $this->data['layout']['title'], $this->data['layout']['section_title'], $this->data['issue']['number'], $this->data['issue']['title']);
	}

	public function fetchSection($issue, $layout) {
		global $app;
		foreach ([1,2] as $part)
			foreach (cockpit('collections:find', sprintf('sections%dx%d', $issue['number'], $part)) as $section)
				foreach ($section['layouts'] as $_layout)
					if ($_layout['value']['_id'] === $layout['_id']) { $section['part'] = $part; return $section; }
		throw new \InvalidArgumentException("Section for layout '$layout[title]' not found.");
	}

	public function fetchLayout($issue, $slug) {
		global $app;
		$layout = cockpit('collections:findOne', sprintf('layouts%d', $issue['number']), compact('slug'));
		if (!$layout) throw new \InvalidArgumentException("Layout '$slug' not found under issue '$issue[title]'.", 404);
		$section = $this->fetchSection($issue, $layout);
		$layout['section_title'] = $section['title'];
		$layout['editorial_url'] = $app->routeLookUp('editorial', ['slug' => $issue['slug']])."#$section[slug]";
		$layout['layouts_url'] = $app->routeLookUp('layouts', ['slug' => $issue['slug'], 'part' => $section['part']]);
		$layout['src'] = $app->imageManager->imageUrl($app->url($layout['image']['path']), 'medium');
		$layout['srcset'] = $app->imageManager->responsiveImageSrcset($app->url($layout['image']['path']), ['medium','large']);
		return $layout;
	}

	protected function fetchData($args) {
		$issue = $this->fetchIssue($args['slug']);
		$layout = $this->fetchLayout($issue, $args['layout']);
		$this->data = array_merge($args, compact('issue','layout'));
	}

}
