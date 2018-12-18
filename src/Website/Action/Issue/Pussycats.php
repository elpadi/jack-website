<?php
namespace Website\Action\Issue;

use Functional as F;
use Website\App;
use Website\Pages;
use Website\Models\Models;

class Pussycats extends JackBlackPussyCat {

	protected function issueAssets() {
		return [
			'css' => ['layouts/full-width','pages/home','pages/models'],
			'js' => ['layouts/infinite-scroll','pages/model','pages/models'],
		];
	}

	protected function metaTitle() {
		return 'Pussycats | '.Issue::metaTitle();
	}

	protected function fetchData($args) {
		Issue::fetchData($args);
		if (isset($_GET['action']) && $_GET['action'] === 'more') {
			if (!isset($_GET['exclude']) || empty($_GET['exclude'])) throw new \InvalidArgumentException("An exclude list must be provided.", 400);
			$model = Models::getOneRandomWithout($_GET['exclude']);
			if (!$model) {
				$this->data['content'] = '';
				return FALSE;
			}
		}
		else {
			$model = Models::getOneRandom();
		}
		$this->data['content'] = App::$container['templates']->snippet('models/list', ['models' => [$model]]);
	}

}
