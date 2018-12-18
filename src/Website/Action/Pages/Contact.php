<?php
namespace Website\Action\Pages;

use Functional as F;
use Jack\Action\Page;

class Contact extends Page {

	protected function fetchData($args) {
		$this->data['contacts'] = cockpit('collections:find', 'contacts');
		$this->data['background'] = \Jack\AssetManager::background('hollywood');
	}

}
