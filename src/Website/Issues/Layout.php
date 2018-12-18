<?php
namespace Website\Issues;

use Functional as F;
use Website\App;
use Website\Data\Object as DataObject;

class Layout extends DataObject {

	public function getResponsiveImage() {
		return $this->getResponsiveLayout($this->image['path']);
	}

	public function getImageSrc($size='large') {
		return App::$container['images']->imageUrl(App::url($this->image['path']), $size);
	}

	public function getUrl(Issue $issue) {
		return App::routeUrl('layout', ['id' => $issue->id, 'slug' => $issue->slug, 'layout' => $this->slug]);
	}

}
