<?php
namespace Website\Action;

use Jack\Action\Page;

class Jbpc extends Page {

	protected function assets() {
		return [
			'css' => ['layouts/full-width','pages/home','pages/models'],
			'js' => ['pages/models'],
		];
	}

}
