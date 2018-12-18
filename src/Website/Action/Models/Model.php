<?php
namespace Website\Action\Models;

use function Stringy\create as s;
use Website\App;
use Website\Models\Models;

class Model extends Base {
	
	protected function assets() {
		return array_merge_recursive(parent::assets(), [
			'js' => ['layouts/infinite-scroll','pages/model'],
		]);
	}

	protected function metaTitle() {
		return sprintf('%s | Jack Magazine', $this->data['model']->name);
	}

	protected function fetchData($args) {
		if ($args['slug'] === 'infinite-scroll') {
			if (!isset($_GET['exclude']) || empty($_GET['exclude'])) throw new \InvalidArgumentException("An exclude list must be provided.", 400);
			$model = Models::getOneRandomWithout($_GET['exclude']);
			if (!$model) {
				$this->data['content'] = '';
				return FALSE;
			}
		}
		elseif (!empty($args['slug'])) {
			$name = ucwords(str_replace('-', ' ', $args['slug']));
			$model = Models::getOne(compact('name'));
			if (!$model) throw new \InvalidArgumentException("Model '$args[slug]' not found.", 404);
			$this->data['model'] = $model;
		}
		else {
			$model = Models::getOneRandom();
			$this->data['model'] = $model;
		}
		$this->data['content'] = App::$container['templates']->snippet('models/list', ['models' => [$model]]);
	}

}
