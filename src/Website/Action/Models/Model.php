<?php
namespace Website\Action\Models;

use function Stringy\create as s;

class Model extends Base {
	
	protected function assets() {
		return array_merge_recursive(parent::assets(), [
			'js' => ['layouts/infinite-scroll','pages/model'],
		]);
	}

	protected function metaTitle() {
		return sprintf('%s | Jack Magazine', $this->data['model']['name']);
	}

	protected function fetchData($args) {
		global $app;
		$models = new \Website\Models();
		if ($args['slug'] === 'infinite-scroll') {
			if (!isset($_GET['exclude']) || empty($_GET['exclude'])) throw new \InvalidArgumentException("An exclude list must be provided.", 400);
			$all = array_filter($models->fetchAll(), function($m) {
				return strpos($_GET['exclude'], (string)s($m['name'])->slugify()) === FALSE;
			});
			shuffle($all);
			$this->data['content'] = $app->templateManager->snippet('models/list', ['models' => array_slice($all, 0, 1)]);
			$this->data['count'] = count($all);
		}
		else {
			$this->data['model'] = $models->fetchBySlug($args['slug']);
			if (!$this->data['model']) throw new \InvalidArgumentException("No model found with name $args[slug].", 404);
			$this->data['content'] = $app->templateManager->snippet('models/list', ['models' => [$this->data['model']]]);
		}
	}

}
