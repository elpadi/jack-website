<?php
namespace Website\Issues;

use Functional as F;
use Website\App;
use Website\DataObject;
use Website\Shop;

class Issue extends DataObject {

	public function getSections() {
		return Sections::getByIssueId($this->id);
	}

	public function getUrl() {
		return App::routeUrl('issue', ['id' => $this->id, 'slug' => $this->slug]);
	}

	public function getLayoutsUrl() {
		return App::routeUrl('layouts', ['id' => $this->id, 'slug' => $this->slug]);
	}

	public function getResponsiveCovers() {
		return array_map([$this, 'getResponsiveLayout'], F\pluck(array_slice($this->covers, 0, 1), 'path'));
	}

}
