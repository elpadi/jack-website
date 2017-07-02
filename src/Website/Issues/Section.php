<?php
namespace Website\Issues;

use Functional as F;
use Website\App;
use Website\DataObject;

class Section extends DataObject {

	public function getLayouts() {
		return Layouts::createFromChildren($this->layouts);
	}

	public function getUrl(Issue $issue) {
		return App::routeUrl('section', ['id' => $issue->id, 'slug' => $issue->slug, 'section' => $this->slug]);
	}

}
