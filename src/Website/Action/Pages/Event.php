<?php
namespace Website\Action\Pages;

use Functional as F;
use Jack\Action\Page;

class Event extends Page {

	protected function fetchData($args) {
		parent::fetchdata($args);
		$this->data['events'] = cockpit('collections:find', 'events');
		$images = cockpit('collections:find', 'deck2016images');
		foreach ($images as &$img) $img['src'] = \Jack\App::instance()->imageManager->imageUrl(\Jack\App::instance()->url($img['image']['path']), 'large');
		$this->data['images'] = $images;
	}

}
