<?php
namespace Website\Action\Models;

class Model extends Base {
	
	protected function metaTitle() {
		return sprintf('%s | Jack Magazine', $this->data['model']['name']);
	}

	protected function fetchData($args) {
		global $app;
		$models = new \Website\Models();
		$this->data['model'] = $models->fetchBySlug($args['slug']);
		$this->data['content'] = $app->templateManager->snippet('models/list', ['models' => [$this->data['model']]]);
	}

}
