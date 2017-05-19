<?php
namespace Website\Action;

use Functional as F;
use Jack\Action\Page;

class Home extends Intro {

	protected function assets() {
		return array_merge_recursive(parent::assets(), [
			'css' => ['pages/intro'],
			'js' => ['pages/intro'],
		]);
	}

}
