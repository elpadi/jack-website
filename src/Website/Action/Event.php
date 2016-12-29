<?php
namespace Website\Action;

use Functional as F;
use Jack\Action\Page;

class Event extends Page {

	protected function fetchData($args) {
		$this->data['events'] = cockpit('collections:find', 'events');
		$this->data['images'] = cockpit('collections:find', 'deck2016images');
	}

}
