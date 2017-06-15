<?php
namespace Website\Action\Models;

use Functional as F;
use Jack\Action\Page;

abstract class Base extends Page {

	protected function assets() {
		return [
			'css' => ['layouts/full-width','pages/models'],
			'js' => ['pages/models'],
		];
	}

}
