<?php
namespace Website;

use Functional as F;

class Action extends \Jack\Action {

	protected function getPage($name) {
		$page = cockpit('collections:findOne', 'blocks', ['title' => ucwords($name)]);
		$page['META_TITLE'] = sprintf('%s | %s', $name === 'intro' ? 'Welcome' : ucwords($name), 'Jack Magazine');
		$page['META_DESCRIPTION'] = cockpit('collections:findOne', 'pagedescriptions', compact('name'));
		return $page;
	}

	protected function getActionCallback($name) {
		$fn = [$this, str_replace('/', '_', $name)];
		if (is_callable($fn)) return $fn;
		if ($this->getPage($name)) return [$this, 'page']; 
		return [$this, '_default'];
	}
	
	protected function parseIssueSlug($slug) {
		preg_match('/([0-9]+)-([a-z-]+)/', $slug, $matches);
		if (!$matches) $app->notFound($response);
		$number = intval($matches[1]);
		$name = $matches[2];
		$issue = cockpit('collections:findOne', 'issues', compact('number'));
		if (!$issue) $app->notFound($response);
		foreach ([1,2] as $part) $issue["part_$part"] = isset($issue["part_$part"]) ? F\map(F\pluck($issue["part_$part"], 'value'), function($item) {
			return cockpit('collections:findOne', 'layouts', ['title' => $item['display']]);
		}) : [];
		return array_merge($issue, compact('name','slug','sections'));
	}

	protected function renderIssue($response, $name, $args) {
		global $app;
		$issue = $this->parseIssueSlug($args['slug']);
		switch ($name) {
		case 'single': $section_title = 'Cover'; break;
		case 'editorial': $section_title = sprintf('Part %d Editorial', $args['part']); break;
		}
		return $response->write($app->render("issues/$name", array_merge(compact('issue'), $args, [
			'META_TITLE' => sprintf('%s | Issue #%d | %s | %s', $issue['title'], $issue['number'], $section_title, 'Jack Magazine'),
		])));
	}

	public function issue($request, $response, $args, $name) {
		return $this->renderIssue($response, 'single', $args);
	}

	public function editorial($request, $response, $args, $name) {
		return $this->renderIssue($response, 'editorial', $args);
	}

	public function contact($request, $response, $args, $name) {
		$args['contacts'] = F\filter(cockpit('collections:find', 'contacts'), function($contact) {
			return isset($contact['visible']) && $contact['visible'];
		});
		return $this->_default($request, $response, $args, $name);
	}

	public function event($request, $response, $args, $name) {
		$args['events'] = cockpit('collections:find', 'events');
		return $this->_default($request, $response, $args, $name);
	}

	public function page($request, $response, $args, $name) {
		return $this->_default($request, $response, array_merge($args, $this->getPage($name)));
	}

}
